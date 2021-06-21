<?php

namespace TCG\Voyager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use TCG\Voyager\Events\MenuDisplay;
use TCG\Voyager\Facades\Voyager;

/**
 * @todo: Refactor this class by using something like MenuBuilder Helper.
 */
class Menu extends Model
{
    protected $table = 'menus';

    protected $guarded = [];

    public static function boot()
    {
        parent::boot();

        static::saved(function ($model) {
            $model->removeMenuFromCache();
        });

        static::deleted(function ($model) {
            $model->removeMenuFromCache();
        });
    }

    public function items()
    {
        return $this->hasMany(Voyager::modelClass('MenuItem'));
    }

    public function parent_items()
    {
        return $this->hasMany(Voyager::modelClass('MenuItem'))
            ->whereNull('parent_id');
    }

    /**
     * Display menu.
     *
     * @param string      $menuName
     * @param string|null $type
     * @param array       $options
     *
     * @return string
     */
    public static function display($menuName, $type = null, array $options = [])
    {
        // GET THE MENU - sort collection in blade
        $menu = \Cache::remember('voyager_menu_'.$menuName, \Carbon\Carbon::now()->addDays(30), function () use ($menuName) {
            return static::where('name', '=', $menuName)
            ->with(['parent_items.children' => function ($q) {
                $q->orderBy('order');
            }])
            ->first();
        });

        // Check for Menu Existence
        if (!isset($menu)) {
            return false;
        }

        event(new MenuDisplay($menu));

        // Convert options array into object
        $options = (object) $options;

        $items = $menu->parent_items->sortBy('order');

        if ($menuName == 'admin' && $type == '_json') {
            $items = static::processItems($items);
        }

        if ($type == 'admin') {
            $type = 'voyager::menu.'.$type;
        } else {
            if (is_null($type)) {
                $type = 'voyager::menu.default';
            } elseif ($type == 'bootstrap' && !view()->exists($type)) {
                $type = 'voyager::menu.bootstrap';
            }
        }

        if (!isset($options->locale)) {
            $options->locale = app()->getLocale();
        }

        $items = static::checkItemPermissions($items);

        if ($type === '_json') {
            return $items;
        }

        return new \Illuminate\Support\HtmlString(
            \Illuminate\Support\Facades\View::make($type, ['items' => $items, 'options' => $options])->render()
        );
    }

    public function removeMenuFromCache()
    {
        \Cache::forget('voyager_menu_'.$this->name);
    }

    private static function checkItemPermissions($items)
    {
        // Filter items by permission
        return $items->filter(function ($item) {

            // check for special "needs_permission" field in parameters.
            $needs_permission = $item->permissions->needs_permission ?? null;
            if ($needs_permission && !Auth::user()->can($needs_permission)) {
                return false;
            }

            // check for special "needs_role" field in parameters.
            $needs_role = $item->permissions->needs_role ?? null;
            if ($needs_role && !Auth::user()->hasRole($needs_role)) {
                return false;
            }

            if ($item->children->count()) {
                $item->setRelation('children', static::checkItemPermissions($item->children));
            }

            return true;
        });
    }

    protected static function processItems($items)
    {
        // Eagerload Translations
        if (config('voyager.multilingual.enabled')) {
            $items->load('translations');
        }

        $items = $items->transform(function ($item) {
            // Translate title
            $item->title = $item->getTranslatedAttribute('title');
            // Resolve URL/Route
            $item->href = $item->link(true);

            if ($item->href == url()->current() && $item->href != '') {
                // The current URL is exactly the URL of the menu-item
                $item->active = true;
            } elseif (Str::startsWith(url()->current(), Str::finish($item->href, '/'))) {
                // The current URL is "below" the menu-item URL. For example "admin/posts/1/edit" => "admin/posts"
                $item->active = true;
            }
            if (($item->href == url('') || $item->href == route('voyager.dashboard')) && $item->children->count() > 0) {
                // Exclude sub-menus
                $item->active = false;
            } elseif ($item->href == route('voyager.dashboard') && url()->current() != route('voyager.dashboard')) {
                // Exclude dashboard
                $item->active = false;
            }

            if ($item->children->count() > 0) {
                $item->setRelation('children', static::processItems($item->children));

                if (!$item->children->where('active', true)->isEmpty()) {
                    $item->active = true;
                }
            }

            return $item;
        });

        // Filter items by permission
        $items = $items->filter(function ($item) {
            return !$item->children->isEmpty() || Auth::user()->can('browse', $item);
        })->filter(function ($item) {
            // Filter out empty menu-items
            if ($item->url == '' && $item->route == '' && $item->children->count() == 0) {
                return false;
            }

            return true;
        });

        return $items->values();
    }
}

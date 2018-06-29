<?php

namespace TCG\Voyager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use TCG\Voyager\Events\MenuDisplay;
use TCG\Voyager\Facades\Voyager;

/**
 * @todo: Refactor this class by using something like MenuBuilder Helper.
 */
class Menu extends Model
{
    protected $table = 'menus';

    protected $guarded = [];

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
     * Returns the menu's cache key
     */
    protected static function cacheKey()
    {
        return sprintf(
            '%s_%s_%s',
            $menuName,
            $type,
            md5(serialize($options))
        );
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
        $cacheKey = self::cacheKey($menuName, $type, $options);

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        // GET THE MENU - sort collection in blade
        $menu = static::where('name', '=', $menuName)
            ->with(['parent_items.children' => function ($q) {
                $q->orderBy('order');
            }])
            ->first();

        // Check for Menu Existence
        if (!isset($menu)) {
            return false;
        }

        event(new MenuDisplay($menu));

        // Convert options array into object
        $options = (object) $options;

        // Set static vars values for admin menus
        if (in_array($type, ['admin', 'admin_menu'])) {
            $permissions = Voyager::model('Permission')->all();
            $dataTypes = Voyager::model('DataType')->all();
            $prefix = trim(route('voyager.dashboard', [], false), '/');
            $user_permissions = null;

            if (!Auth::guest()) {
                $user = Voyager::model('User')->find(Auth::id());
                $user_permissions = $user->role ? $user->role->permissions->pluck('key')->toArray() : [];
            }

            $options->user = (object) compact('permissions', 'dataTypes', 'prefix', 'user_permissions');

            // change type to blade template name - TODO funky names, should clean up later
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

        $menu_html = new \Illuminate\Support\HtmlString(
            \Illuminate\Support\Facades\View::make($type, [
                'items' => $menu->parent_items->sortBy('order'),
                'options' => $options
            ])->render()
        );

        // Tagging not supported in file/database caches
        if (config('cache.default') === 'file' || config('cache.default') === 'database') {
            Cache::forever($cacheKey, $menu_html);
        } else {
            Cache::tags(['voyager-menu'])->forever($cacheKey, $menu_html);
        }

        return $menu_html;
    }
}

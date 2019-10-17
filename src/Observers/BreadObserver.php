<?php

namespace TCG\Voyager\Observers;

use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Models\DataType;

class BreadObserver
{
    public function updated(DataType $bread)
    {
        if (config('voyager.bread.add_menu_item') && file_exists(base_path('routes/web.php'))) {
            $attributes = $bread->getDirty();

            $menu = Voyager::model('Menu')->where('name', config('voyager.bread.default_menu'))->firstOrFail();

            $route = array_key_exists('slug', $attributes)
                ? $bread->getOriginal('slug')
                : $bread->slug;

            $menuItem = Voyager::model('MenuItem')->whereRoute('voyager.'.$route.'.index')->first();

            if (!$menuItem) {
                return;
            }

            $menuItem->update([
                'title'      => array_key_exists('display_name_plural', $attributes) ? $attributes['display_name_plural'] : $menuItem->title,
                'route'      => array_key_exists('slug', $attributes) ? 'voyager.'.$attributes['slug'].'.index' : $menuItem->route,
                'icon_class' => array_key_exists('icon', $attributes) ? $attributes['icon'] : $menuItem->icon_class,
            ]);
        }
    }
}

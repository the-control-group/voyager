<?php

namespace TCG\Voyager\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Models\Permission;
use TCG\Voyager\Models\Role;

class VoyagerMenuController extends Controller
{
    public function builder($id)
    {
        $menu = Voyager::model('Menu')->findOrFail($id);

        $this->authorize('edit', $menu);

        $isModelTranslatable = is_bread_translatable(Voyager::model('MenuItem'));
        $permissions = Permission::all();
        $roles = Role::all();

        return Voyager::view('voyager::menus.builder', compact('menu', 'isModelTranslatable', 'permissions', 'roles'));
    }

    public function delete_menu($menu, $id)
    {
        $item = Voyager::model('MenuItem')->findOrFail($id);

        $this->authorize('delete', $item);

        $item->deleteAttributeTranslation('title');

        $item->destroy($id);

        return redirect()
            ->route('voyager.menus.builder', [$menu])
            ->with([
                'message'    => __('voyager::menu_builder.successfully_deleted'),
                'alert-type' => 'success',
            ]);
    }

    public function add_item(Request $request)
    {
        $menu = Voyager::model('Menu');

        $this->authorize('add', $menu);

        $data = $this->prepareParameters(
            $request->all()
        );

        unset($data['id']);
        $data['order'] = Voyager::model('MenuItem')->highestOrderMenuItem();

        // Check if is translatable
        $_isTranslatable = is_bread_translatable(Voyager::model('MenuItem'));
        if ($_isTranslatable) {
            // Prepare data before saving the menu
            $trans = $this->prepareMenuTranslations($data);
        }

        $menuItem = Voyager::model('MenuItem')->create($data);

        // Save menu translations
        if ($_isTranslatable) {
            $menuItem->setAttributeTranslations('title', $trans, true);
        }

        return redirect()
            ->route('voyager.menus.builder', [$data['menu_id']])
            ->with([
                'message'    => __('voyager::menu_builder.successfully_created'),
                'alert-type' => 'success',
            ]);
    }

    public function update_item(Request $request)
    {
        $id = $request->input('id');
        $data = $this->prepareParameters(
            $request->except(['id'])
        );

        $menuItem = Voyager::model('MenuItem')->findOrFail($id);

        $this->authorize('edit', $menuItem->menu);

        if (is_bread_translatable($menuItem)) {
            $trans = $this->prepareMenuTranslations($data);

            // Save menu translations
            $menuItem->setAttributeTranslations('title', $trans, true);
        }

        $menuItem->update($data);

        return redirect()
            ->route('voyager.menus.builder', [$menuItem->menu_id])
            ->with([
                'message'    => __('voyager::menu_builder.successfully_updated'),
                'alert-type' => 'success',
            ]);
    }

    public function order_item(Request $request)
    {
        $menuItemOrder = json_decode($request->input('order'));

        $this->orderMenu($menuItemOrder, null);
    }

    private function orderMenu(array $menuItems, $parentId)
    {
        foreach ($menuItems as $index => $menuItem) {
            $item = Voyager::model('MenuItem')->findOrFail($menuItem->id);
            $item->order = $index + 1;
            $item->parent_id = $parentId;
            $item->save();

            if (isset($menuItem->children)) {
                $this->orderMenu($menuItem->children, $item->id);
            }
        }
    }

    protected function prepareParameters($parameters)
    {
        switch (Arr::get($parameters, 'type')) {
            case 'route':
                $parameters['url'] = null;
                break;
            default:
                $parameters['route'] = null;
                $parameters['parameters'] = '';
                break;
        }

        if (isset($parameters['type'])) {
            unset($parameters['type']);
        }

        return $parameters;
    }

    /**
     * Prepare menu translations.
     *
     * @param array $data menu data
     *
     * @return JSON translated item
     */
    protected function prepareMenuTranslations(&$data)
    {
        $trans = json_decode($data['title_i18n'], true);

        // Set field value with the default locale
        $data['title'] = $trans[config('voyager.multilingual.default', 'en')];

        unset($data['title_i18n']);     // Remove hidden input holding translations
        unset($data['i18n_selector']);  // Remove language selector input radio

        return $trans;
    }
}

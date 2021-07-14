<?php

namespace TCG\Voyager\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use ReflectionClass;
use TCG\Voyager\Database\Schema\SchemaManager;
use TCG\Voyager\Database\Schema\Table;
use TCG\Voyager\Database\Types\Type;
use TCG\Voyager\Events\BreadAdded;
use TCG\Voyager\Events\BreadDeleted;
use TCG\Voyager\Events\BreadUpdated;
use TCG\Voyager\Facades\Voyager;

class VoyagerBreadController extends Controller
{
    public function index()
    {
        $this->authorize('browse_bread');

        $dataTypes = Voyager::model('DataType')->select('id', 'name', 'slug')->get()->keyBy('name')->toArray();

        $tables = array_map(function ($table) use ($dataTypes) {
            $table = Str::replaceFirst(DB::getTablePrefix(), '', $table);

            $table = [
                'prefix'     => DB::getTablePrefix(),
                'name'       => $table,
                'slug'       => $dataTypes[$table]['slug'] ?? null,
                'dataTypeId' => $dataTypes[$table]['id'] ?? null,
            ];

            return (object) $table;
        }, SchemaManager::listTableNames());

        return Voyager::view('voyager::tools.bread.index')->with(compact('dataTypes', 'tables'));
    }

    /**
     * Create BREAD.
     *
     * @param Request $request
     * @param string  $table   Table name.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Request $request, $table)
    {
        $this->authorize('browse_bread');

        $dataType = Voyager::model('DataType')->whereName($table)->first();

        $data = $this->prepopulateBreadInfo($table);
        $data['fieldOptions'] = SchemaManager::describeTable(
            (isset($dataType) && strlen($dataType->model_name) != 0)
            ? DB::getTablePrefix().app($dataType->model_name)->getTable()
            : DB::getTablePrefix().$table
        );

        return Voyager::view('voyager::tools.bread.edit-add', $data);
    }

    private function prepopulateBreadInfo($table)
    {
        $displayName = Str::singular(implode(' ', explode('_', Str::title($table))));
        $modelNamespace = config('voyager.models.namespace', app()->getNamespace());
        if (empty($modelNamespace)) {
            $modelNamespace = app()->getNamespace();
        }

        return [
            'isModelTranslatable'  => true,
            'table'                => $table,
            'slug'                 => Str::slug($table),
            'display_name'         => $displayName,
            'display_name_plural'  => Str::plural($displayName),
            'model_name'           => $modelNamespace.Str::studly(Str::singular($table)),
            'generate_permissions' => true,
            'server_side'          => false,
        ];
    }

    /**
     * Store BREAD.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $this->authorize('browse_bread');

        try {
            $dataType = Voyager::model('DataType');
            $res = $dataType->updateDataType($request->all(), true);
            $data = $res
                ? $this->alertSuccess(__('voyager::bread.success_created_bread'))
                : $this->alertError(__('voyager::bread.error_creating_bread'));
            if ($res) {
                event(new BreadAdded($dataType, $data));
            }

            return redirect()->route('voyager.bread.index')->with($data);
        } catch (\Exception $e) {
            return redirect()->route('voyager.bread.index')->with($this->alertException($e, 'Saving Failed'));
        }
    }

    /**
     * Edit BREAD.
     *
     * @param string $table
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($table)
    {
        $this->authorize('browse_bread');

        $dataType = Voyager::model('DataType')->whereName($table)->first();

        $fieldOptions = SchemaManager::describeTable(
            (strlen($dataType->model_name) != 0)
            ? DB::getTablePrefix().app($dataType->model_name)->getTable()
            : DB::getTablePrefix().$dataType->name
        );

        $isModelTranslatable = is_bread_translatable($dataType);
        $tables = SchemaManager::listTableNames();
        $dataTypeRelationships = Voyager::model('DataRow')->where('data_type_id', '=', $dataType->id)->where('type', '=', 'relationship')->get();
        $scopes = [];
        if ($dataType->model_name != '') {
            $scopes = $this->getModelScopes($dataType->model_name);
        }

        return Voyager::view('voyager::tools.bread.edit-add', compact('dataType', 'fieldOptions', 'isModelTranslatable', 'tables', 'dataTypeRelationships', 'scopes'));
    }

    /**
     * Update BREAD.
     *
     * @param \Illuminate\Http\Request $request
     * @param number                   $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function update(Request $request, $id)
    {
        $this->authorize('browse_bread');

        /* @var \TCG\Voyager\Models\DataType $dataType */
        try {
            $dataType = Voyager::model('DataType')->find($id);

            // Prepare Translations and Transform data
            $translations = is_bread_translatable($dataType)
                ? $dataType->prepareTranslations($request)
                : [];

            $res = $dataType->updateDataType($request->all(), true);
            $data = $res
                ? $this->alertSuccess(__('voyager::bread.success_update_bread', ['datatype' => $dataType->name]))
                : $this->alertError(__('voyager::bread.error_updating_bread'));
            if ($res) {
                event(new BreadUpdated($dataType, $data));
            }

            // Save translations if applied
            $dataType->saveTranslations($translations);

            return redirect()->route('voyager.bread.index')->with($data);
        } catch (\Exception $e) {
            return back()->with($this->alertException($e, __('voyager::generic.update_failed')));
        }
    }

    /**
     * Delete BREAD.
     *
     * @param Number $id BREAD data_type id.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $this->authorize('browse_bread');

        /* @var \TCG\Voyager\Models\DataType $dataType */
        $dataType = Voyager::model('DataType')->find($id);

        // Delete Translations, if present
        if (is_bread_translatable($dataType)) {
            $dataType->deleteAttributeTranslations($dataType->getTranslatableAttributes());
        }

        $res = Voyager::model('DataType')->destroy($id);
        $data = $res
            ? $this->alertSuccess(__('voyager::bread.success_remove_bread', ['datatype' => $dataType->name]))
            : $this->alertError(__('voyager::bread.error_updating_bread'));
        if ($res) {
            event(new BreadDeleted($dataType, $data));
        }

        if (!is_null($dataType)) {
            Voyager::model('Permission')->removeFrom($dataType->name);
        }

        return redirect()->route('voyager.bread.index')->with($data);
    }

    /**
     * Return the list of model's scopes that have only one required parameters: the $query.
     *
     * @param string $model_name
     * @return \Illuminate\Support\Collection
     */
    public function getModelScopes($model_name)
    {
        $reflection = new ReflectionClass($model_name);

        return collect($reflection->getMethods())->filter(function ($method) {
            return ( Str::startsWith($method->name, 'scope')
                && $method->getNumberOfRequiredParameters() == 1 );
        })->whereNotIn('name', ['scopeWithTranslations', 'scopeWithTranslation', 'scopeWhereTranslation'])->transform(function ($method) {
            return lcfirst(Str::replaceFirst('scope', '', $method->name));
        });
    }

    // ************************************************************
    //  _____      _       _   _                 _     _
    // |  __ \    | |     | | (_)               | |   (_)
    // | |__) |___| | __ _| |_ _  ___  _ __  ___| |__  _ _ __  ___
    // |  _  // _ \ |/ _` | __| |/ _ \| '_ \/ __| '_ \| | '_ \/ __|
    // | | \ \  __/ | (_| | |_| | (_) | | | \__ \ | | | | |_) \__ \
    // |_|  \_\___|_|\__,_|\__|_|\___/|_| |_|___/_| |_|_| .__/|___/
    //                                                  | |
    //                                                  |_|
    // ************************************************************

    /**
     * Add Relationship.
     *
     * @param Request $request
     */
    public function addRelationship(Request $request)
    {
        $relationshipField = $this->getRelationshipField($request);

        if (!class_exists($request->relationship_model)) {
            return back()->with([
                'message'    => 'Model Class '.$request->relationship_model.' does not exist. Please create Model before creating relationship.',
                'alert-type' => 'error',
            ]);
        }

        try {
            DB::beginTransaction();

            $relationship_column = $request->relationship_column_belongs_to;
            if ($request->relationship_type == 'hasOne' || $request->relationship_type == 'hasMany') {
                $relationship_column = $request->relationship_column;
            }

            // Build the relationship details
            $relationshipDetails = [
                'model'       => $request->relationship_model,
                'table'       => $request->relationship_table,
                'type'        => $request->relationship_type,
                'column'      => $relationship_column,
                'key'         => $request->relationship_key,
                'label'       => $request->relationship_label,
                'pivot_table' => $request->relationship_pivot,
                'pivot'       => ($request->relationship_type == 'belongsToMany') ? '1' : '0',
                'taggable'    => $request->relationship_taggable,
            ];

            $className = Voyager::modelClass('DataRow');
            $newRow = new $className();

            $newRow->data_type_id = $request->data_type_id;
            $newRow->field = $relationshipField;
            $newRow->type = 'relationship';
            $newRow->display_name = $request->relationship_table;
            $newRow->required = 0;

            foreach (['browse', 'read', 'edit', 'add', 'delete'] as $check) {
                $newRow->{$check} = 1;
            }

            $newRow->details = $relationshipDetails;
            $newRow->order = intval(Voyager::model('DataType')->find($request->data_type_id)->lastRow()->order) + 1;

            if (!$newRow->save()) {
                return back()->with([
                    'message'    => 'Error saving new relationship row for '.$request->relationship_table,
                    'alert-type' => 'error',
                ]);
            }

            DB::commit();

            return back()->with([
                'message'    => 'Successfully created new relationship for '.$request->relationship_table,
                'alert-type' => 'success',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with([
                'message'    => 'Error creating new relationship: '.$e->getMessage(),
                'alert-type' => 'error',
            ]);
        }
    }

    /**
     * Get Relationship Field.
     *
     * @param Request $request
     *
     * @return string
     */
    private function getRelationshipField($request)
    {
        // We need to make sure that we aren't creating an already existing field

        $dataType = Voyager::model('DataType')->find($request->data_type_id);

        $field = Str::singular($dataType->name).'_'.$request->relationship_type.'_'.Str::singular($request->relationship_table).'_relationship';

        $relationshipFieldOriginal = $relationshipField = strtolower($field);

        $existingRow = Voyager::model('DataRow')->where('field', '=', $relationshipField)->first();
        $index = 1;

        while (isset($existingRow->id)) {
            $relationshipField = $relationshipFieldOriginal.'_'.$index;
            $existingRow = Voyager::model('DataRow')->where('field', '=', $relationshipField)->first();
            $index += 1;
        }

        return $relationshipField;
    }

    /**
     * Delete Relationship.
     *
     * @param Number $id Record id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteRelationship($id)
    {
        Voyager::model('DataRow')->destroy($id);

        return back()->with([
            'message'    => 'Successfully deleted relationship.',
            'alert-type' => 'success',
        ]);
    }
}

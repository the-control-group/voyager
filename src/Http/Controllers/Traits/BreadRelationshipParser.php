<?php

namespace TCG\Voyager\Http\Controllers\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Pagination\LengthAwarePaginator;
use TCG\Voyager\Models\DataType;

trait BreadRelationshipParser
{
    protected $patchId;

    /**
     * Build the relationships array for the model's eager load.
     *
     * @param DataType $dataType
     *
     * @return array
     */
    protected function getRelationships(DataType $dataType)
    {
        $relationships = [];

        $dataType->browseRows->each(function ($item) use (&$relationships) {
            $details = json_decode($item->details);
            if (isset($details->relationship) && isset($item->field)) {
                $relation = $details->relationship;
                if (isset($relation->method)) {
                    $method = $relation->method;
                    $this->patchId[$method] = true;
                } else {
                    $method = camel_case($item->field);
                    $this->patchId[$method] = false;
                }

                if (strpos($relation->key, '.') > 0) {
                    $this->patchId[$method] = false;
                }

                 // select only what we need
                $relationships[$method] = function ($query) use ($relation) {
                    $table = $query->getRelated()->getTable();

                    $query->select($table . "." . $relation->key, $table . "." . $relation->label);
                };
            }
        });

        return $relationships;
    }

    /**
     * Replace relationships' keys for labels and create READ links if a slug is provided.
     *
     * @param  $dataTypeContent     Can be either an eloquent Model, Collection or LengthAwarePaginator instance.
     * @param DataType $dataType
     *
     * @return $dataTypeContent
     */
    protected function resolveRelations($dataTypeContent, DataType $dataType)
    {
        // In case of using server-side pagination, we need to work on the Collection (BROWSE)
        if ($dataTypeContent instanceof LengthAwarePaginator) {
            $dataTypeCollection = $dataTypeContent->getCollection();
        }
        // If it's a model just make the changes directly on it (READ / EDIT)
        elseif ($dataTypeContent instanceof Model) {
            return $this->relationToLink($dataTypeContent, $dataType);
        }
        // Or we assume it's a Collection
        else {
            $dataTypeCollection = $dataTypeContent;
        }

        $dataTypeCollection->transform(function ($item) use ($dataType) {
            return $this->relationToLink($item, $dataType);
        });

        return $dataTypeContent instanceof LengthAwarePaginator ? $dataTypeContent->setCollection($dataTypeCollection) : $dataTypeCollection;
    }

    /**
     * Create the URL for relationship's anchors in BROWSE and READ views.
     *
     * @param Model    $item     Object to modify
     * @param DataType $dataType
     *
     * @return Model $item
     */
    protected function relationToLink(Model $item, DataType $dataType)
    {
        $relations = $item->getRelations();

        if (!empty($relations) && array_filter($relations)) {
            foreach ($relations as $field => $relation) {
                if (is_null($relation) || ($relation instanceof Collection && $relation->isEmpty())) continue;

                if ($this->patchId[$field]) {
                    $field = $this->getField($item, $field);
                } else {
                    $field = snake_case($field);
                }

                $bread_data = $dataType->browseRows->where('field', $field)->first();
                $relationData = json_decode($bread_data->details)->relationship;

                if (!is_object($item[$field]) && isset($item[$field])) {
                    $item[$field] = $relation[$relationData->label];
                } elseif (isset($item[$field])) {
                    $tmp = $item[$field];
                    $item[$field] = $tmp;
                } else {
                    $item[$field] = $item[$relationData->method];
                }

                if (isset($relationData->page_slug)) {
                    $id = $relation->id;
                    $item[$field.'_page_slug'] = url($relationData->page_slug, $id);
                }
            }
        }

        return $item;
    }

    protected function getField($item, $relationMethod)
    {
        $relationBuilder = $item->$relationMethod();

        // original field named was used so we'll return that
        if ($relationBuilder instanceof BelongsTo) return $relationBuilder->getForeignKey();

        $relatedModel = $relationBuilder->getRelated();

        // While adding the relationship we used getRelationName() as field name,
        // so we'll return that now too.
        return $relatedModel->getTable() . "_" . $relatedModel->getKeyName();
    }
}

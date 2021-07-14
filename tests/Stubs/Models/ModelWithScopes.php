<?php

namespace TCG\Voyager\Tests\Stubs\Models ;

/**
 * This class to test BREAD model's scope(s).
 */
class ModelWithScopes {

    /**
     * A scope without custom parameter.
     * @param  $query  \Illuminate\Database\Eloquent\Builder
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithOutCustomParameter( \Illuminate\Database\Eloquent\Builder $query )
    {
        return $query->where('foo', true);
    }

    /**
     * A scope with one custom parameter.
     * @param  $query  \Illuminate\Database\Eloquent\Builder
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithOptionalCustomParameter( \Illuminate\Database\Eloquent\Builder $query, $aScopeParameter = null )
    {
        return $query->where('foo', true);
    }

    /**
     * A scope with one mandatory custom parameter.
     * @param  $query  \Illuminate\Database\Eloquent\Builder
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithMandatoryCustomParameter( \Illuminate\Database\Eloquent\Builder $query, $aScopeParameter )
    {
        return $query->where('foo', true);
    }

}

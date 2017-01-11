<?php

namespace TCG\Voyager\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'voyager_categories';

    protected $fillable = ['slug', 'name'];

    public function posts()
    {
        return $this->hasMany(Post::class)
            ->published()
            ->orderBy('created_at', 'DESC');
    }
}

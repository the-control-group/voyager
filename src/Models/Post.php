<?php

namespace TCG\Voyager\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Traits\Translatable;

class Post extends Model
{
    use Translatable;

    protected $translatable = ['title', 'seo_title', 'excerpt', 'body', 'slug', 'meta_description', 'meta_keywords'];

    const PUBLISHED = 'PUBLISHED';

    protected $guarded = [];

    public function save(array $options = [])
    {
        // If no author has been assigned, assign the current user's id as the author of the post
        if (!$this->author_id && Auth::user()) {
            $this->author_id = Auth::user()->id;
        }

        parent::save();
    }

    public function authorId()
    {
        return $this->belongsTo(Voyager::modelClass('User'));
    }

    /**
     * Scope a query to only published scopes.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublished(Builder $query)
    {
        return $query->where('status', '=', static::PUBLISHED);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function category()
    {
        return $this->hasOne(Voyager::modelClass('Category'), 'id', 'category_id');
    }

    /**
     * @param null $slug
     * @param null $id
     * @param bool $author
     *
     * @return \Illuminate\Support\HtmlString
     *
     * @author Dusan Perisic
     */
    public static function display(string $slug = null, string $id = null, array $author = null, $model = self::class)
    {
        $data = $model::where($slug ? 'slug' : 'id', $slug ? $slug : $id)->get()->first();
        $data->author = $author;
        if ($data->author) {
            $data->author = User::find($data->author_id, $author)->toArray();
        }

        return new \Illuminate\Support\HtmlString(
            \Illuminate\Support\Facades\View::make('voyager::posts.show', $data)->render()
        );
    }
}

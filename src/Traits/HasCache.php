<?php 

namespace TCG\Voyager\Traits;

use Illuminate\Contracts\Cache\Repository as Cache;

trait HasCache
{
    protected static $cache_repository = 1;
    protected static $events_that_clear_cache = ['created', 'deleted', 'saved', 'updated'];

    public static function BootHasCache()
    {
        self::$cache_repository = app(Cache::class);

        foreach (self::$events_that_clear_cache as $event) {
            static::$event(function () {
                self::clearCache();
            });
        }
    }

    public static function getCached()
    {
        return self::$cache_repository->rememberForever(self::getCacheKey(), function () {
            return static::all();
        });
    }

    public static function clearCache()
    {
        self::$cache_repository->forget(self::getCacheKey());
    }

    protected static function getCacheKey($key = null)
    {
        return 'has_cache_'.get_class().(!is_null($key) ? '_'.$key : '');
    }
}

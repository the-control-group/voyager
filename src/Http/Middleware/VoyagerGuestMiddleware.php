<?php

namespace TCG\Voyager\Http\Middleware;

use Closure;

class VoyagerGuestMiddleware
{
    public function handle($request, Closure $next)
    {
        if (auth()->guest()) {
            return $next($request);
        }

        return redirect()->back();
    }
}

<?php

namespace TCG\Voyager\Tests\Unit\Middleware;

use TCG\Voyager\Http\Middleware\VoyagerGuestMiddleware;
use TCG\Voyager\Tests\TestCase;

class VoyagerGuestMiddlewareTest extends TestCase
{
    /**
     * @test
     */
    public function logged_in_user_is_redirected()
    {
        $user = \Auth::loginUsingId(1);
        $this->actingAs($user);

        $request = request()->create('/admin/password/reset', 'GET');

        $middleware = new VoyagerGuestMiddleware();

        $response = $middleware->handle($request, function () {
        });

        $this->assertEquals($response->getStatusCode(), 302);
    }

    /**
     * @test
     */
    public function guests_can_pass_through_middleware()
    {
        $request = request()->create('/admin/password/reset', 'GET');

        $middleware = new VoyagerGuestMiddleware();

        $response = $middleware->handle($request, function () {
        });

        $this->assertEquals($response, null);
    }
}

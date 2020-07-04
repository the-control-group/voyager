<?php

namespace TCG\Voyager\Tests\Feature;

use http\Env\Request;
use TCG\Voyager\Tests\TestCase;

class ResetPasswordTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->install();
    }

//    /**
//     * @test
//     */
//    public function can_access_forgot_password_page()
//    {
//        $this->visit(route('voyager.password.request'))
//            ->see(__('voyager::auth.request_password'));
//    }
}

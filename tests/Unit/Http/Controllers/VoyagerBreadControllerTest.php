<?php

namespace TCG\Voyager\Tests\Unit\Controllers;

use TCG\Voyager\Actions\AbstractAction;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Models\User;
use TCG\Voyager\Tests\TestCase;
use TCG\Voyager\Tests\Stubs\Models\ModelWithScopes ;
use TCG\Voyager\Http\Controllers\VoyagerBreadController;

class VoyagerBreadControllerTest extends TestCase
{
    /**
     * @see \TCG\Voyager\Tests\Stubs\Models\ModelWithScopes fake model with test dedicated scopes.
     * @return void
     */
    public function testGetModelScopes()
    {
        $vbc = new VoyagerBreadController();
        $scopes = $vbc->getModelScopes( ModelWithScopes::class );
        $this->assertNotEmpty( $scopes );
        $this->assertEquals( 2, count($scopes) );
    }
}

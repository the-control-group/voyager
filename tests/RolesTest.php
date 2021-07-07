<?php

namespace TCG\Voyager\Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use TCG\Voyager\Models\Role;

class RolesTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;
    protected $permission_id = 3;

    public function setUp(): void
    {
        parent::setUp();

        Auth::loginUsingId(1);
        $this->user = Auth::user();
    }

    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function testRoles()
    {
        // Adding a New Role
        $this->visit(route('voyager.roles.create'))
             ->type('superadmin', 'name')
             ->type('Super Admin', 'display_name')
             ->press(__('voyager::generic.save'))
             ->seePageIs(route('voyager.roles.index'))
             ->seeInDatabase('roles', ['name' => 'superadmin']);

        // Editing a Role
        $this->visit(route('voyager.roles.edit', 2))
             ->type('regular_user', 'name')
             ->press(__('voyager::generic.save'))
             ->seePageIs(route('voyager.roles.index'))
             ->seeInDatabase('roles', ['name' => 'regular_user']);

        // Editing a Role
        $this->visit(route('voyager.roles.edit', 2))
             ->type('user', 'name')
             ->press(__('voyager::generic.save'))
             ->seePageIs(route('voyager.roles.index'))
             ->seeInDatabase('roles', ['name' => 'user']);

        // Get the current super admin role
        $superadmin_role = Role::where('name', '=', 'superadmin')->first();

        // Deleting a Role
        $response = $this->call('DELETE', route('voyager.roles.destroy', $superadmin_role->id), ['_token' => csrf_token()]);
        $this->assertEquals(302, $response->getStatusCode());
        $this->notSeeInDatabase('roles', ['name' => 'superadmin']);
    }

    /**
     * Edit role permissions.
     *
     * @return void
     */
    public function testEditRolePermissions()
    {
        $this->notSeeInDatabase('permission_role', ['permission_id' => $this->permission_id, 'role_id' => 2]);
        Role::find(2)->permissions()->attach($this->permission_id);

        $this->visit(route('voyager.roles.edit', 2))
             ->uncheck('permissions['.$this->permission_id.']')
             ->press(__('voyager::generic.save'))
             ->seePageIs(route('voyager.roles.index'))
             ->notSeeInDatabase('permission_role', ['permission_id' => $this->permission_id, 'role_id' => 2]);
    }
}

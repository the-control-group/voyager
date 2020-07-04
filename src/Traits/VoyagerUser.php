<?php

namespace TCG\Voyager\Traits;

use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Notifications\VoyagerPasswordResetNotification;

/**
 * @property  \Illuminate\Database\Eloquent\Collection  roles
 */
trait VoyagerUser
{
    /**
     * Return default User Role.
     */
    public function role()
    {
        return $this->belongsTo(Voyager::modelClass('Role'));
    }

    /**
     * Return alternative User Roles.
     */
    public function roles()
    {
        return $this->belongsToMany(Voyager::modelClass('Role'), 'user_roles', 'user_id', 'role_id');
    }

    /**
     * Return all User Roles, merging the default and alternative roles.
     */
    public function roles_all()
    {
        $this->loadRolesRelations();

        return collect([$this->role])->merge($this->roles);
    }

    /**
     * Check if User has a Role(s) associated.
     *
     * @param string|array $name The role(s) to check.
     *
     * @return bool
     */
    public function hasRole($name)
    {
        $roles = $this->roles_all()->pluck('name')->toArray();

        foreach ((is_array($name) ? $name : [$name]) as $role) {
            if (in_array($role, $roles)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Set default User Role.
     *
     * @param string $name The role name to associate.
     */
    public function setRole($name)
    {
        $role = Voyager::model('Role')->where('name', '=', $name)->first();

        if ($role) {
            $this->role()->associate($role);
            $this->save();
        }

        return $this;
    }

    public function hasPermission($name)
    {
        $this->loadPermissionsRelations();

        $_permissions = $this->roles_all()
                              ->pluck('permissions')->flatten()
                              ->pluck('key')->unique()->toArray();

        return in_array($name, $_permissions);
    }

    public function hasPermissionOrFail($name)
    {
        if (!$this->hasPermission($name)) {
            throw new UnauthorizedHttpException(null);
        }

        return true;
    }

    public function hasPermissionOrAbort($name, $statusCode = 403)
    {
        if (!$this->hasPermission($name)) {
            return abort($statusCode);
        }

        return true;
    }

    private function loadRolesRelations()
    {
        if (!$this->relationLoaded('role')) {
            $this->load('role');
        }

        if (!$this->relationLoaded('roles')) {
            $this->load('roles');
        }
    }

    private function loadPermissionsRelations()
    {
        $this->loadRolesRelations();

        if ($this->role && !$this->role->relationLoaded('permissions')) {
            $this->role->load('permissions');
            $this->load('roles.permissions');
        }
    }

    /**
     * Send the password reset notification.
     *
     * @param string $token
     *
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new VoyagerPasswordResetNotification($token));
    }
}

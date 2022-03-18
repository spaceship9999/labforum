<?php

namespace App\Traits;

use App\Models\Permission;
use App\Models\Role;

trait PermissionTrait {
    public function grantPermissionToUser(...$permissions) {

        $permissions = $this->getValidPermissions($permissions);
        //From these valid permissions, grant the user on every permissions
        if ($permissions === null) return $this;
        $this->permissions()->saveMany($permissions);
    }

    public function revokePermissionFromUser(...$permissions) {

        $permissions = $this->getValidPermissions($permissions);
        //From these valid permissions, revoke the user on every permissions
        $this->permissions()->detach($permissions);

    }

    public function getValidPermissions(array $permissions) {

        return Permission::whereIn('slug', $permissions)->get();
    }

    public function hasPermission($permission) :bool {

        return $this->hasInheritedPermissionFromRole($permission) ||
            $this->permissions()->where('slug', $permission);
    }

    public function hasInheritedPermissionFromRole($permission) :bool {
        foreach ($permission->roles as $role) {
            if($this->roles->contains($role)){
                return true;
            }
        }
        return false;
    }


    public function hasRole(... $roles) {
        if (empty($this->roles)) return false;
        foreach ($roles as $role) {
            //Check if user roles consists of the role slug
            if ($this->roles->contains('slug', $role)) return true;
        }
        return false;
    }

    public function roles() {
        return $this->belongsToMany(Role::class, 'users_roles');
    }

    public function permissions() {
        return $this->belongsToMany(Permission::class, 'users_permissions');
    }
}
<?php

namespace App\Traits;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasRolesAndPermissions
{
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user')->withTimestamps();
    }

    public function permissionOverrides(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'permission_user')
            ->withPivot('is_granted', 'assigned_by_user_id')
            ->withTimestamps();
    }

    public function hasRole(string|array $roles): bool
    {
        if (is_array($roles)) {
            return $this->roles->whereIn('name', $roles)->isNotEmpty();
        }
        return $this->roles->contains('name', $roles);
    }

    public function hasPermission(string $permissionKey): bool
    {
        // 1. Check direct user-level overrides first
        $override = $this->permissionOverrides->firstWhere('name', $permissionKey);
        if ($override) {
            return (bool) $override->pivot->is_granted;
        }

        // 2. Admin role gets all permissions unless explicitly revoked above
        if ($this->hasRole('admin')) {
            return true;
        }

        // 3. Check role-assigned permissions
        foreach ($this->roles as $role) {
            if ($role->permissions->contains('name', $permissionKey)) {
                return true;
            }
        }

        return false;
    }

    public function assignRole(string|Role $role): void
    {
        $roleModel = is_string($role) ? Role::where('name', $role)->firstOrFail() : $role;
        if (!$this->roles->contains('id', $roleModel->id)) {
            $this->roles()->attach($roleModel->id);
            $this->load('roles');
        }
    }

    public function removeRole(string|Role $role): void
    {
        $roleModel = is_string($role) ? Role::where('name', $role)->first() : $role;
        if ($roleModel) {
            $this->roles()->detach($roleModel->id);
            $this->load('roles');
        }
    }

    public function setPermissionOverride(string|Permission $permission, bool $isGranted = true, ?int $assignedById = null): void
    {
        $permModel = is_string($permission) ? Permission::where('name', $permission)->firstOrFail() : $permission;
        $this->permissionOverrides()->syncWithoutDetaching([
            $permModel->id => [
                'is_granted' => $isGranted,
                'assigned_by_user_id' => $assignedById,
            ]
        ]);
        $this->load('permissionOverrides');
    }

    public function removePermissionOverride(string|Permission $permission): void
    {
        $permModel = is_string($permission) ? Permission::where('name', $permission)->first() : $permission;
        if ($permModel) {
            $this->permissionOverrides()->detach($permModel->id);
            $this->load('permissionOverrides');
        }
    }
}

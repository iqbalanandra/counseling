<?php

namespace App\Infrastructure\Persistence\Repositories;

use App\Domain\Role\Repositories\RoleRepositoryInterface;
use App\Models\Role;
use Illuminate\Database\Eloquent\Collection;

class RoleRepository implements RoleRepositoryInterface
{
    public function all(): Collection
    {
        return Role::with('permissions')->withCount('users')->get();
    }

    public function findById(int $id): ?Role
    {
        return Role::with(['permissions', 'users'])->find($id);
    }

    public function findByName(string $name): ?Role
    {
        return Role::with('permissions')->where('name', $name)->first();
    }

    public function create(array $data): Role
    {
        return Role::create($data);
    }

    public function update(Role $role, array $data): bool
    {
        return $role->update($data);
    }

    public function delete(Role $role): bool
    {
        return $role->delete();
    }

    public function syncPermissions(Role $role, array $permissionIds): void
    {
        $role->permissions()->sync($permissionIds);
    }
}

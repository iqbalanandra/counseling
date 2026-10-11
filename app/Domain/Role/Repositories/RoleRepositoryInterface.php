<?php

namespace App\Domain\Role\Repositories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Collection;

interface RoleRepositoryInterface
{
    public function all(): Collection;

    public function findById(int $id): ?Role;

    public function findByName(string $name): ?Role;

    public function create(array $data): Role;

    public function update(Role $role, array $data): bool;

    public function delete(Role $role): bool;

    public function syncPermissions(Role $role, array $permissionIds): void;
}

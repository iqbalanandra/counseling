<?php

namespace App\Infrastructure\Persistence\Repositories;

use App\Domain\Permission\Repositories\PermissionRepositoryInterface;
use App\Models\Permission;
use Illuminate\Database\Eloquent\Collection;

class PermissionRepository implements PermissionRepositoryInterface
{
    public function all(): Collection
    {
        return Permission::all();
    }

    public function allGrouped(): array
    {
        return Permission::all()->groupBy('group')->toArray();
    }

    public function findById(int $id): ?Permission
    {
        return Permission::find($id);
    }

    public function findByName(string $name): ?Permission
    {
        return Permission::where('name', $name)->first();
    }

    public function create(array $data): Permission
    {
        return Permission::create($data);
    }
}

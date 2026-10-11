<?php

namespace App\Domain\Permission\Repositories;

use App\Models\Permission;
use Illuminate\Database\Eloquent\Collection;

interface PermissionRepositoryInterface
{
    public function all(): Collection;

    public function allGrouped(): array;

    public function findById(int $id): ?Permission;

    public function findByName(string $name): ?Permission;

    public function create(array $data): Permission;
}

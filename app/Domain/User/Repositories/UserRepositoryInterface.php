<?php

namespace App\Domain\User\Repositories;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface UserRepositoryInterface
{
    public function paginate(int $perPage = 15, ?string $search = null, ?string $role = null): LengthAwarePaginator;

    public function findById(int $id): ?User;

    public function findByEmail(string $email): ?User;

    public function create(array $data): User;

    public function update(User $user, array $data): bool;

    public function delete(User $user): bool;

    public function syncRoles(User $user, array $roleIds): void;
}

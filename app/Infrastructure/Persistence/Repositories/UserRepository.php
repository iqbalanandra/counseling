<?php

namespace App\Infrastructure\Persistence\Repositories;

use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class UserRepository implements UserRepositoryInterface
{
    public function paginate(int $perPage = 15, ?string $search = null, ?string $role = null): LengthAwarePaginator
    {
        return User::query()
            ->with(['roles', 'student.schoolClass', 'teacher'])
            ->when($search, function (Builder $query, string $search) {
                $query->where(function (Builder $q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('identifier_number', 'like', "%{$search}%");
                });
            })
            ->when($role, function (Builder $query, string $role) {
                $query->whereHas('roles', fn ($q) => $q->where('name', $role));
            })
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function findById(int $id): ?User
    {
        return User::with(['roles.permissions', 'student.schoolClass', 'teacher.homeroomClass'])->find($id);
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function update(User $user, array $data): bool
    {
        return $user->update($data);
    }

    public function delete(User $user): bool
    {
        return $user->delete();
    }

    public function syncRoles(User $user, array $roleIds): void
    {
        $user->roles()->sync($roleIds);
    }
}

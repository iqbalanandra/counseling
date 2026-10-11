<?php

namespace App\Application\UseCases\User;

use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Infrastructure\Services\AuditLogService;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateUserUseCase
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository
    ) {}

    public function execute(array $data): User
    {
        $user = $this->userRepository->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'identifier_number' => $data['identifier_number'] ?? null,
            'status' => $data['status'] ?? 'active',
            'password' => Hash::make($data['password']),
            'email_verified_at' => now(),
        ]);

        if (! empty($data['role_ids'])) {
            $this->userRepository->syncRoles($user, $data['role_ids']);
        }

        AuditLogService::log(
            action: 'create',
            module: 'user',
            description: "Membuat akun pengguna baru: {$user->name} ({$user->email})",
            resourceId: (string) $user->id,
            newValues: ['name' => $user->name, 'email' => $user->email, 'status' => $user->status]
        );

        return $user;
    }
}

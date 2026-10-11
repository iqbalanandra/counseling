<?php

namespace App\Application\UseCases\User;

use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Infrastructure\Services\AuditLogService;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UpdateUserUseCase
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository
    ) {}

    public function execute(User $user, array $data): bool
    {
        $oldData = ['name' => $user->name, 'email' => $user->email, 'status' => $user->status];

        $updateData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'identifier_number' => $data['identifier_number'] ?? null,
            'status' => $data['status'] ?? 'active',
        ];

        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $updated = $this->userRepository->update($user, $updateData);

        if (isset($data['role_ids'])) {
            $this->userRepository->syncRoles($user, $data['role_ids']);
        }

        AuditLogService::log(
            action: 'update',
            module: 'user',
            description: "Memperbarui akun pengguna: {$user->name}",
            resourceId: (string) $user->id,
            oldValues: $oldData,
            newValues: ['name' => $data['name'], 'email' => $data['email'], 'status' => $data['status'] ?? 'active']
        );

        return $updated;
    }
}

<?php

namespace App\Application\UseCases\Role;

use App\Application\DTOs\RoleDTO;
use App\Domain\Role\Repositories\RoleRepositoryInterface;
use App\Infrastructure\Services\AuditLogService;
use App\Models\Role;
use Illuminate\Validation\ValidationException;

class UpdateRoleUseCase
{
    public function __construct(
        private readonly RoleRepositoryInterface $roleRepository
    ) {}

    public function execute(Role $role, RoleDTO $dto): bool
    {
        if ($role->is_protected && $role->name !== $dto->name) {
            throw ValidationException::withMessages([
                'name' => 'Nama role yang terproteksi tidak dapat diubah.',
            ]);
        }

        $oldData = ['name' => $role->name, 'display_name' => $role->display_name];

        $updated = $this->roleRepository->update($role, [
            'name' => $dto->name,
            'display_name' => $dto->displayName,
            'description' => $dto->description,
        ]);

        $this->roleRepository->syncPermissions($role, $dto->permissionIds);

        AuditLogService::log(
            action: 'update',
            module: 'role',
            description: "Memperbarui role: {$role->display_name}",
            resourceId: (string) $role->id,
            oldValues: $oldData,
            newValues: ['name' => $dto->name, 'display_name' => $dto->displayName, 'permissions' => $dto->permissionIds]
        );

        return $updated;
    }
}

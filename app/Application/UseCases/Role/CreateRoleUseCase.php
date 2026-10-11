<?php

namespace App\Application\UseCases\Role;

use App\Application\DTOs\RoleDTO;
use App\Domain\Role\Repositories\RoleRepositoryInterface;
use App\Infrastructure\Services\AuditLogService;
use App\Models\Role;

class CreateRoleUseCase
{
    public function __construct(
        private readonly RoleRepositoryInterface $roleRepository
    ) {}

    public function execute(RoleDTO $dto): Role
    {
        $role = $this->roleRepository->create([
            'name' => $dto->name,
            'display_name' => $dto->displayName,
            'description' => $dto->description,
            'is_protected' => false,
        ]);

        if (! empty($dto->permissionIds)) {
            $this->roleRepository->syncPermissions($role, $dto->permissionIds);
        }

        AuditLogService::log(
            action: 'create',
            module: 'role',
            description: "Membuat role baru: {$role->display_name} ({$role->name})",
            resourceId: (string) $role->id,
            newValues: ['name' => $role->name, 'display_name' => $role->display_name, 'permissions' => $dto->permissionIds]
        );

        return $role;
    }
}

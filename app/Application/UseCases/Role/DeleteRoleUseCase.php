<?php

namespace App\Application\UseCases\Role;

use App\Domain\Role\Repositories\RoleRepositoryInterface;
use App\Infrastructure\Services\AuditLogService;
use App\Models\Role;
use Illuminate\Validation\ValidationException;

class DeleteRoleUseCase
{
    public function __construct(
        private readonly RoleRepositoryInterface $roleRepository
    ) {}

    public function execute(Role $role): bool
    {
        if ($role->is_protected) {
            throw ValidationException::withMessages([
                'role' => 'Role yang terproteksi tidak dapat dihapus.',
            ]);
        }

        $name = $role->display_name;
        $id = $role->id;

        $deleted = $this->roleRepository->delete($role);

        if ($deleted) {
            AuditLogService::log(
                action: 'delete',
                module: 'role',
                description: "Menghapus role: {$name}",
                resourceId: (string) $id
            );
        }

        return $deleted;
    }
}

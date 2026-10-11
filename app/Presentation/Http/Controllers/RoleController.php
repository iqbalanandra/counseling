<?php

namespace App\Presentation\Http\Controllers;

use App\Application\DTOs\RoleDTO;
use App\Application\UseCases\Role\CreateRoleUseCase;
use App\Application\UseCases\Role\DeleteRoleUseCase;
use App\Application\UseCases\Role\GetRolesUseCase;
use App\Application\UseCases\Role\UpdateRoleUseCase;
use App\Domain\Permission\Repositories\PermissionRepositoryInterface;
use App\Domain\Role\Repositories\RoleRepositoryInterface;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RoleController extends Controller
{
    public function __construct(
        private readonly GetRolesUseCase $getRoles,
        private readonly CreateRoleUseCase $createRole,
        private readonly UpdateRoleUseCase $updateRole,
        private readonly DeleteRoleUseCase $deleteRole,
        private readonly PermissionRepositoryInterface $permissionRepository,
        private readonly RoleRepositoryInterface $roleRepository,
    ) {}

    public function index(): Response
    {
        $this->authorize('role.view');

        return Inertia::render('Roles/Index', [
            'roles' => $this->getRoles->execute()->map(fn ($r) => [
                'id' => $r->id,
                'name' => $r->name,
                'display_name' => $r->display_name,
                'description' => $r->description,
                'is_protected' => $r->is_protected,
                'users_count' => $r->users_count,
                'permissions_count' => $r->permissions->count(),
            ]),
        ]);
    }

    public function show(Role $role): Response
    {
        $this->authorize('role.view');

        $role->load('permissions');
        $permissions = $this->permissionRepository->all()->groupBy('group');

        return Inertia::render('Roles/Show', [
            'role' => [
                'id' => $role->id,
                'name' => $role->name,
                'display_name' => $role->display_name,
                'description' => $role->description,
                'is_protected' => $role->is_protected,
                'permission_ids' => $role->permissions->pluck('id')->toArray(),
            ],
            'groupedPermissions' => $permissions->map(fn ($perms) => $perms->map(fn ($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'display_name' => $p->display_name,
                'description' => $p->description,
            ])),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('role.create');

        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:roles,name',
            'display_name' => 'required|string|max:150',
            'description' => 'nullable|string|max:500',
            'permission_ids' => 'nullable|array',
            'permission_ids.*' => 'integer|exists:permissions,id',
        ]);

        $role = $this->createRole->execute(RoleDTO::fromRequest($validated));

        return back()->with('success', "Role '{$role->display_name}' berhasil dibuat.");
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $this->authorize('role.update');

        $validated = $request->validate([
            'name' => "required|string|max:100|unique:roles,name,{$role->id}",
            'display_name' => 'required|string|max:150',
            'description' => 'nullable|string|max:500',
            'permission_ids' => 'nullable|array',
            'permission_ids.*' => 'integer|exists:permissions,id',
        ]);

        $this->updateRole->execute($role, RoleDTO::fromRequest($validated));

        return back()->with('success', "Role '{$role->display_name}' berhasil diperbarui.");
    }

    public function destroy(Role $role): RedirectResponse
    {
        $this->authorize('role.delete');

        $this->deleteRole->execute($role);

        return redirect()->route('roles.index')->with('success', "Role '{$role->display_name}' berhasil dihapus.");
    }
}

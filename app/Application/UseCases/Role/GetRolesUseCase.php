<?php

namespace App\Application\UseCases\Role;

use App\Domain\Role\Repositories\RoleRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class GetRolesUseCase
{
    public function __construct(
        private readonly RoleRepositoryInterface $roleRepository
    ) {}

    public function execute(): Collection
    {
        return $this->roleRepository->all();
    }
}

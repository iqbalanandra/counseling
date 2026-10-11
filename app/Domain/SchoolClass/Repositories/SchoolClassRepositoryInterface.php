<?php

namespace App\Domain\SchoolClass\Repositories;

use App\Models\SchoolClass;
use Illuminate\Database\Eloquent\Collection;

interface SchoolClassRepositoryInterface
{
    public function all(): Collection;

    public function findById(int $id): ?SchoolClass;

    public function findByHomeroomTeacherId(int $teacherId): ?SchoolClass;

    public function create(array $data): SchoolClass;

    public function update(SchoolClass $schoolClass, array $data): bool;

    public function delete(SchoolClass $schoolClass): bool;
}

<?php

namespace App\Domain\Student\Repositories;

use App\Models\Student;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface StudentRepositoryInterface
{
    public function paginate(int $perPage = 15, ?string $search = null, ?int $classId = null, ?array $allowedClassIds = null): LengthAwarePaginator;

    public function findById(int $id): ?Student;

    public function findByUserId(int $userId): ?Student;

    public function findByNis(string $nis): ?Student;

    public function getByClassId(int $classId): Collection;

    public function create(array $data): Student;

    public function update(Student $student, array $data): bool;

    public function delete(Student $student): bool;
}

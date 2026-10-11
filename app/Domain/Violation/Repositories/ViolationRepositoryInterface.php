<?php

namespace App\Domain\Violation\Repositories;

use App\Models\Violation;
use App\Models\ViolationCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ViolationRepositoryInterface
{
    public function getActiveCategories(): Collection;

    public function paginate(int $perPage = 15, ?int $studentId = null, ?int $classId = null, ?int $categoryId = null, ?string $search = null, ?array $allowedClassIds = null): LengthAwarePaginator;

    public function findById(int $id): ?Violation;

    public function create(array $data): Violation;

    public function update(Violation $violation, array $data): bool;

    public function delete(Violation $violation): bool;

    public function getStudentViolationHistory(int $studentId): Collection;
}

<?php

namespace App\Domain\Achievement\Repositories;

use App\Models\Achievement;
use App\Models\AchievementCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface AchievementRepositoryInterface
{
    public function getActiveCategories(): Collection;

    public function paginate(int $perPage = 15, ?int $studentId = null, ?int $classId = null, ?int $categoryId = null, ?string $search = null, ?array $allowedClassIds = null): LengthAwarePaginator;

    public function findById(int $id): ?Achievement;

    public function create(array $data): Achievement;

    public function update(Achievement $achievement, array $data): bool;

    public function delete(Achievement $achievement): bool;

    public function getStudentAchievements(int $studentId): Collection;
}

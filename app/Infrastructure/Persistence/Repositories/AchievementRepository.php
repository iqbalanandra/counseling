<?php

namespace App\Infrastructure\Persistence\Repositories;

use App\Domain\Achievement\Repositories\AchievementRepositoryInterface;
use App\Models\Achievement;
use App\Models\AchievementCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class AchievementRepository implements AchievementRepositoryInterface
{
    public function getActiveCategories(): Collection
    {
        return AchievementCategory::where('is_active', true)->get();
    }

    public function paginate(int $perPage = 15, ?int $studentId = null, ?int $classId = null, ?int $categoryId = null, ?string $search = null, ?array $allowedClassIds = null): LengthAwarePaginator
    {
        return Achievement::query()
            ->with(['student.schoolClass', 'category', 'recordedBy'])
            ->when($studentId, fn (Builder $q) => $q->where('student_id', $studentId))
            ->when($classId, fn (Builder $q) => $q->whereHas('student', fn ($sq) => $sq->where('class_id', $classId)))
            ->when($allowedClassIds !== null, fn (Builder $q) => $q->whereHas('student', fn ($sq) => $sq->whereIn('class_id', $allowedClassIds)))
            ->when($categoryId, fn (Builder $q) => $q->where('achievement_category_id', $categoryId))
            ->when($search, function (Builder $q, string $search) {
                $q->whereHas('student', fn ($sq) => $sq->where('name', 'like', "%{$search}%")->orWhere('nis', 'like', "%{$search}%"));
            })
            ->latest('event_date')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function findById(int $id): ?Achievement
    {
        return Achievement::with(['student.schoolClass', 'category', 'recordedBy'])->find($id);
    }

    public function create(array $data): Achievement
    {
        $achievement = Achievement::create($data);
        if ($achievement->verification_status === 'verified') {
            $achievement->student->increment('total_achievement_points', $achievement->points);
        }

        return $achievement;
    }

    public function update(Achievement $achievement, array $data): bool
    {
        $oldPoints = $achievement->points;
        $oldStatus = $achievement->verification_status;
        $updated = $achievement->update($data);

        if ($updated) {
            $newPoints = $data['points'] ?? $oldPoints;
            $newStatus = $data['verification_status'] ?? $oldStatus;

            if ($oldStatus === 'verified' && $newStatus !== 'verified') {
                $achievement->student->decrement('total_achievement_points', $oldPoints);
            } elseif ($oldStatus !== 'verified' && $newStatus === 'verified') {
                $achievement->student->increment('total_achievement_points', $newPoints);
            } elseif ($newStatus === 'verified' && $newPoints !== $oldPoints) {
                $achievement->student->increment('total_achievement_points', $newPoints - $oldPoints);
            }
        }

        return $updated;
    }

    public function delete(Achievement $achievement): bool
    {
        $points = $achievement->points;
        $status = $achievement->verification_status;
        $student = $achievement->student;
        $deleted = $achievement->delete();

        if ($deleted && $status === 'verified') {
            $student->decrement('total_achievement_points', $points);
        }

        return $deleted;
    }

    public function getStudentAchievements(int $studentId): Collection
    {
        return Achievement::with(['category', 'recordedBy'])
            ->where('student_id', $studentId)
            ->latest('event_date')
            ->get();
    }
}

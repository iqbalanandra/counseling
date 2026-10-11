<?php

namespace App\Infrastructure\Persistence\Repositories;

use App\Domain\Violation\Repositories\ViolationRepositoryInterface;
use App\Models\Violation;
use App\Models\ViolationCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ViolationRepository implements ViolationRepositoryInterface
{
    public function getActiveCategories(): Collection
    {
        return ViolationCategory::where('is_active', true)->get();
    }

    public function paginate(int $perPage = 15, ?int $studentId = null, ?int $classId = null, ?int $categoryId = null, ?string $search = null, ?array $allowedClassIds = null): LengthAwarePaginator
    {
        return Violation::query()
            ->with(['student.schoolClass', 'category', 'recordedBy'])
            ->when($studentId, fn (Builder $q) => $q->where('student_id', $studentId))
            ->when($classId, fn (Builder $q) => $q->whereHas('student', fn ($sq) => $sq->where('class_id', $classId)))
            ->when($allowedClassIds !== null, fn (Builder $q) => $q->whereHas('student', fn ($sq) => $sq->whereIn('class_id', $allowedClassIds)))
            ->when($categoryId, fn (Builder $q) => $q->where('violation_category_id', $categoryId))
            ->when($search, function (Builder $q, string $search) {
                $q->whereHas('student', fn ($sq) => $sq->where('name', 'like', "%{$search}%")->orWhere('nis', 'like', "%{$search}%"));
            })
            ->latest('violation_date')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function findById(int $id): ?Violation
    {
        return Violation::with(['student.schoolClass', 'category', 'recordedBy'])->find($id);
    }

    public function create(array $data): Violation
    {
        $violation = Violation::create($data);
        // Increment student violation points
        $violation->student->increment('total_violation_points', $violation->points);

        return $violation;
    }

    public function update(Violation $violation, array $data): bool
    {
        $oldPoints = $violation->points;
        $updated = $violation->update($data);
        if ($updated && isset($data['points']) && $data['points'] !== $oldPoints) {
            $diff = $data['points'] - $oldPoints;
            $violation->student->increment('total_violation_points', $diff);
        }

        return $updated;
    }

    public function delete(Violation $violation): bool
    {
        $points = $violation->points;
        $student = $violation->student;
        $deleted = $violation->delete();
        if ($deleted) {
            $student->decrement('total_violation_points', $points);
        }

        return $deleted;
    }

    public function getStudentViolationHistory(int $studentId): Collection
    {
        return Violation::with(['category', 'recordedBy'])
            ->where('student_id', $studentId)
            ->latest('violation_date')
            ->get();
    }
}

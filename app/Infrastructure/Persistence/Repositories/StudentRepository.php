<?php

namespace App\Infrastructure\Persistence\Repositories;

use App\Domain\Student\Repositories\StudentRepositoryInterface;
use App\Models\Student;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class StudentRepository implements StudentRepositoryInterface
{
    public function paginate(int $perPage = 15, ?string $search = null, ?int $classId = null, ?array $allowedClassIds = null): LengthAwarePaginator
    {
        return Student::query()
            ->with(['schoolClass.homeroomTeacher', 'user'])
            ->when($allowedClassIds !== null, function (Builder $query) use ($allowedClassIds) {
                $query->whereIn('class_id', $allowedClassIds);
            })
            ->when($search, function (Builder $query, string $search) {
                $query->where(function (Builder $q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('nis', 'like', "%{$search}%")
                        ->orWhere('nisn', 'like', "%{$search}%");
                });
            })
            ->when($classId, function (Builder $query, int $classId) {
                $query->where('class_id', $classId);
            })
            ->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function findById(int $id): ?Student
    {
        return Student::with(['schoolClass.homeroomTeacher', 'user'])->find($id);
    }

    public function findByUserId(int $userId): ?Student
    {
        return Student::with(['schoolClass.homeroomTeacher', 'user'])->where('user_id', $userId)->first();
    }

    public function findByNis(string $nis): ?Student
    {
        return Student::with(['schoolClass.homeroomTeacher', 'user'])->where('nis', $nis)->first();
    }

    public function getByClassId(int $classId): Collection
    {
        return Student::where('class_id', $classId)->orderBy('name')->get();
    }

    public function create(array $data): Student
    {
        return Student::create($data);
    }

    public function update(Student $student, array $data): bool
    {
        return $student->update($data);
    }

    public function delete(Student $student): bool
    {
        return $student->delete();
    }
}

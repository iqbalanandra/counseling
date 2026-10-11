<?php

namespace App\Infrastructure\Persistence\Repositories;

use App\Domain\SchoolClass\Repositories\SchoolClassRepositoryInterface;
use App\Models\SchoolClass;
use Illuminate\Database\Eloquent\Collection;

class SchoolClassRepository implements SchoolClassRepositoryInterface
{
    public function all(): Collection
    {
        return SchoolClass::with(['academicYear', 'homeroomTeacher', 'homeroomTeachers.user'])->withCount('students')->orderBy('grade')->orderBy('name')->get();
    }

    public function findById(int $id): ?SchoolClass
    {
        return SchoolClass::with(['academicYear', 'homeroomTeacher', 'homeroomTeachers.user', 'students'])->find($id);
    }

    public function findByHomeroomTeacherId(int $teacherId): ?SchoolClass
    {
        return SchoolClass::with(['academicYear', 'homeroomTeacher', 'homeroomTeachers.user', 'students'])->where('homeroom_teacher_id', $teacherId)->first();
    }

    public function create(array $data): SchoolClass
    {
        return SchoolClass::create($data);
    }

    public function update(SchoolClass $schoolClass, array $data): bool
    {
        return $schoolClass->update($data);
    }

    public function delete(SchoolClass $schoolClass): bool
    {
        return $schoolClass->delete();
    }
}

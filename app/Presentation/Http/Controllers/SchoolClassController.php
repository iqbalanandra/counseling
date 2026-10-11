<?php

namespace App\Presentation\Http\Controllers;

use App\Domain\SchoolClass\Repositories\SchoolClassRepositoryInterface;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Teacher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SchoolClassController extends Controller
{
    public function __construct(
        private readonly SchoolClassRepositoryInterface $classRepository,
    ) {}

    public function index(Request $request): Response
    {
        $this->authorize('student.view');

        $classes = $this->classRepository->all()->map(function ($c) {
            $teachers = $c->homeroomTeachers->map(fn ($t) => [
                'id' => $t->id,
                'name' => $t->name,
                'nip' => $t->nip,
            ]);

            // If pivot is empty but legacy homeroomTeacher exists, include it
            if ($teachers->isEmpty() && $c->homeroomTeacher) {
                $teachers = collect([[
                    'id' => $c->homeroomTeacher->id,
                    'name' => $c->homeroomTeacher->name,
                    'nip' => $c->homeroomTeacher->nip,
                ]]);
            }

            return [
                'id' => $c->id,
                'name' => $c->name,
                'grade' => $c->grade,
                'students_count' => $c->students_count ?? 0,
                'academic_year_id' => $c->academic_year_id,
                'academic_year_name' => $c->academicYear?->name,
                'teachers' => $teachers->values()->all(),
                'teacher_ids' => $teachers->pluck('id')->values()->all(),
            ];
        });

        $teachers = Teacher::with('user')->orderBy('name')->get()->map(fn ($t) => [
            'id' => $t->id,
            'name' => $t->name,
            'nip' => $t->nip,
            'position' => $t->position,
        ]);

        $academicYears = AcademicYear::orderBy('name', 'desc')->get()->map(fn ($y) => [
            'id' => $y->id,
            'name' => $y->name,
            'semester' => $y->semester,
            'is_active' => $y->is_active,
        ]);

        return Inertia::render('Classes/Index', [
            'classes' => $classes,
            'teachers' => $teachers,
            'academicYears' => $academicYears,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('student.create');

        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'grade' => 'required|string|max:20',
            'academic_year_id' => 'nullable|integer|exists:academic_years,id',
            'teacher_ids' => 'nullable|array',
            'teacher_ids.*' => 'integer|exists:teachers,id',
        ]);

        $teacherIds = $validated['teacher_ids'] ?? [];
        $primaryTeacherId = ! empty($teacherIds) ? $teacherIds[0] : null;

        $schoolClass = $this->classRepository->create([
            'name' => $validated['name'],
            'grade' => $validated['grade'],
            'academic_year_id' => $validated['academic_year_id'] ?? null,
            'homeroom_teacher_id' => $primaryTeacherId,
        ]);

        if (! empty($teacherIds)) {
            $schoolClass->homeroomTeachers()->sync($teacherIds);
        }

        return back()->with('success', "Kelas '{$schoolClass->name}' berhasil ditambahkan.");
    }

    public function update(Request $request, SchoolClass $class): RedirectResponse
    {
        $this->authorize('student.update');

        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'grade' => 'required|string|max:20',
            'academic_year_id' => 'nullable|integer|exists:academic_years,id',
            'teacher_ids' => 'nullable|array',
            'teacher_ids.*' => 'integer|exists:teachers,id',
        ]);

        $teacherIds = $validated['teacher_ids'] ?? [];
        $primaryTeacherId = ! empty($teacherIds) ? $teacherIds[0] : null;

        $this->classRepository->update($class, [
            'name' => $validated['name'],
            'grade' => $validated['grade'],
            'academic_year_id' => $validated['academic_year_id'] ?? null,
            'homeroom_teacher_id' => $primaryTeacherId,
        ]);

        $class->homeroomTeachers()->sync($teacherIds);

        return back()->with('success', "Data kelas '{$class->name}' berhasil diperbarui.");
    }

    public function destroy(SchoolClass $class): RedirectResponse
    {
        $this->authorize('student.delete');

        $studentCount = $class->students()->count();
        if ($studentCount > 0) {
            return back()->with('error', "Kelas '{$class->name}' tidak dapat dihapus karena masih memiliki {$studentCount} siswa terdaftar. Pindahkan siswa terlebih dahulu.");
        }

        $className = $class->name;
        $class->homeroomTeachers()->detach();
        $this->classRepository->delete($class);

        return back()->with('success', "Kelas '{$className}' berhasil dihapus.");
    }
}

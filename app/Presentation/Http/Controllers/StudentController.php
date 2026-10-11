<?php

namespace App\Presentation\Http\Controllers;

use App\Domain\SchoolClass\Repositories\SchoolClassRepositoryInterface;
use App\Domain\Student\Repositories\StudentRepositoryInterface;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class StudentController extends Controller
{
    public function __construct(
        private readonly StudentRepositoryInterface $studentRepository,
        private readonly SchoolClassRepositoryInterface $classRepository,
    ) {}

    public function index(Request $request): Response
    {
        $this->authorize('student.view');

        $user = Auth::user();
        $allowedClassIds = null;
        $classes = $this->classRepository->all();

        if ($user->isWaliKelasOnly()) {
            $allowedClassIds = $user->getHomeroomClassIds();
            $classes = $classes->whereIn('id', $allowedClassIds)->values();
        }

        $students = $this->studentRepository->paginate(
            perPage: 15,
            search: $request->query('search'),
            classId: $request->query('class_id') ? (int) $request->query('class_id') : null,
            allowedClassIds: $allowedClassIds
        );

        return Inertia::render('Students/Index', [
            'students' => $students->through(fn ($s) => [
                'id' => $s->id,
                'nis' => $s->nis,
                'nisn' => $s->nisn,
                'name' => $s->name,
                'gender' => $s->gender,
                'status' => $s->status,
                'class_name' => $s->schoolClass?->name,
                'class_id' => $s->class_id,
                'total_violation_points' => $s->total_violation_points,
                'total_achievement_points' => $s->total_achievement_points,
            ]),
            'filters' => $request->only('search', 'class_id'),
            'classes' => $classes->map(fn ($c) => ['id' => $c->id, 'name' => $c->name]),
        ]);
    }

    public function show(Student $student): Response
    {
        $this->authorize('student.view');

        $user = Auth::user();
        if ($user->isWaliKelasOnly()) {
            $allowedClassIds = $user->getHomeroomClassIds();
            if (! in_array($student->class_id, $allowedClassIds, false)) {
                abort(403, 'Anda tidak memiliki hak akses untuk melihat data siswa di luar kelas yang Anda walikan.');
            }
        }

        $student->load(['schoolClass.homeroomTeacher', 'user', 'violations.category', 'achievements.category']);

        return Inertia::render('Students/Show', [
            'student' => [
                'id' => $student->id,
                'nis' => $student->nis,
                'nisn' => $student->nisn,
                'name' => $student->name,
                'gender' => $student->gender,
                'birth_place' => $student->birth_place,
                'birth_date' => $student->birth_date?->toDateString(),
                'address' => $student->address,
                'parent_name' => $student->parent_name,
                'parent_phone' => $student->parent_phone,
                'status' => $student->status,
                'class_name' => $student->schoolClass?->name,
                'homeroom_teacher' => $student->schoolClass?->homeroomTeacher?->name,
                'total_violation_points' => $student->total_violation_points,
                'total_achievement_points' => $student->total_achievement_points,
                'violations' => $student->violations->map(fn ($v) => [
                    'id' => $v->id,
                    'category' => $v->category?->name,
                    'violation_date' => $v->violation_date?->toDateString(),
                    'points' => $v->points,
                    'status' => $v->status,
                    'description' => $v->description,
                ]),
                'achievements' => $student->achievements->map(fn ($a) => [
                    'id' => $a->id,
                    'title' => $a->title,
                    'level' => $a->level,
                    'rank' => $a->rank,
                    'event_date' => $a->event_date?->toDateString(),
                    'verification_status' => $a->verification_status,
                ]),
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('student.create');

        $validated = $request->validate([
            'nis' => 'required|string|max:30|unique:students,nis',
            'nisn' => 'nullable|string|max:20|unique:students,nisn',
            'name' => 'required|string|max:255',
            'gender' => 'required|in:L,P',
            'birth_place' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'address' => 'nullable|string',
            'parent_name' => 'nullable|string|max:255',
            'parent_phone' => 'nullable|string|max:20',
            'class_id' => 'nullable|integer|exists:classes,id',
            'status' => 'required|in:active,graduated,transferred',
        ]);

        $student = $this->studentRepository->create($validated);

        return back()->with('success', "Data siswa '{$student->name}' berhasil ditambahkan.");
    }

    public function update(Request $request, Student $student): RedirectResponse
    {
        $this->authorize('student.update');

        $validated = $request->validate([
            'nis' => "required|string|max:30|unique:students,nis,{$student->id}",
            'nisn' => "nullable|string|max:20|unique:students,nisn,{$student->id}",
            'name' => 'required|string|max:255',
            'gender' => 'required|in:L,P',
            'birth_place' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'address' => 'nullable|string',
            'parent_name' => 'nullable|string|max:255',
            'parent_phone' => 'nullable|string|max:20',
            'class_id' => 'nullable|integer|exists:classes,id',
            'status' => 'required|in:active,graduated,transferred',
        ]);

        $this->studentRepository->update($student, $validated);

        return back()->with('success', "Data siswa '{$student->name}' berhasil diperbarui.");
    }

    public function destroy(Student $student): RedirectResponse
    {
        $this->authorize('student.delete');

        $name = $student->name;
        $this->studentRepository->delete($student);

        return redirect()->route('students.index')->with('success', "Data siswa '{$name}' berhasil dihapus.");
    }
}

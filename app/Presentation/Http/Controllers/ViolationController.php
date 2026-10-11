<?php

namespace App\Presentation\Http\Controllers;

use App\Domain\Violation\Repositories\ViolationRepositoryInterface;
use App\Domain\Student\Repositories\StudentRepositoryInterface;
use App\Domain\SchoolClass\Repositories\SchoolClassRepositoryInterface;
use App\Models\Violation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ViolationController extends Controller
{
    public function __construct(
        private readonly ViolationRepositoryInterface $violationRepository,
        private readonly StudentRepositoryInterface $studentRepository,
        private readonly SchoolClassRepositoryInterface $classRepository,
    ) {}

    public function index(Request $request): Response
    {
        $this->authorize('violation.view');

        $user = Auth::user();
        $classId = null;
        $studentId = null;
        $allowedClassIds = null;
        $classes = $this->classRepository->all();

        // Wali Kelas: only their assigned class(es)
        if ($user->isWaliKelasOnly()) {
            $allowedClassIds = $user->getHomeroomClassIds();
            $classes = $classes->whereIn('id', $allowedClassIds)->values();
        }

        // Student: only own violations
        if ($user->hasRole('Siswa') && !$user->isSuperAdmin()) {
            $student = $this->studentRepository->findByUserId($user->id);
            $studentId = $student?->id;
        }

        $violations = $this->violationRepository->paginate(
            perPage: 15,
            studentId: $request->query('student_id') ? (int) $request->query('student_id') : $studentId,
            classId: $request->query('class_id') ? (int) $request->query('class_id') : $classId,
            categoryId: $request->query('category_id') ? (int) $request->query('category_id') : null,
            search: $request->query('search'),
            allowedClassIds: $allowedClassIds
        );

        $studentsQuery = \App\Models\Student::with('schoolClass')
            ->where('status', 'active');

        if ($allowedClassIds !== null) {
            $studentsQuery->whereIn('class_id', $allowedClassIds);
        } elseif ($classId) {
            $studentsQuery->where('class_id', $classId);
        }

        $students = $studentsQuery->orderBy('name')->get()->map(fn ($s) => [
            'id' => $s->id,
            'name' => $s->name,
            'nis' => $s->nis,
            'class_name' => $s->schoolClass?->name ?? 'Tanpa Kelas',
        ]);

        return Inertia::render('Violations/Index', [
            'violations' => $violations->through(fn ($v) => [
                'id' => $v->id,
                'student_name' => $v->student?->name,
                'student_nis' => $v->student?->nis,
                'class_name' => $v->student?->schoolClass?->name,
                'category' => $v->category?->name,
                'violation_date' => $v->violation_date?->toDateString(),
                'points' => $v->points,
                'status' => $v->status,
                'recorded_by' => $v->recordedBy?->name,
            ]),
            'filters' => $request->only('search', 'class_id', 'category_id', 'student_id'),
            'categories' => $this->violationRepository->getActiveCategories()->map(fn ($c) => ['id' => $c->id, 'name' => $c->name, 'points' => $c->points]),
            'classes' => $classes->map(fn ($c) => ['id' => $c->id, 'name' => $c->name]),
            'students' => $students,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('violation.create');

        $user = Auth::user();

        $validated = $request->validate([
            'student_id' => 'required|integer|exists:students,id',
            'violation_category_id' => 'required|integer|exists:violation_categories,id',
            'violation_date' => 'required|date',
            'points' => 'required|integer|min:1|max:100',
            'description' => 'nullable|string|max:500',
            'sanction_given' => 'nullable|string|max:500',
        ]);

        if ($user->isWaliKelasOnly()) {
            $allowedClassIds = $user->getHomeroomClassIds();
            $targetStudent = \App\Models\Student::find($validated['student_id']);
            if (!$targetStudent || !in_array($targetStudent->class_id, $allowedClassIds, false)) {
                abort(403, 'Anda hanya dapat mencatat pelanggaran untuk siswa di kelas yang Anda walikan.');
            }
        }

        $validated['recorded_by_user_id'] = Auth::id();
        $violation = $this->violationRepository->create($validated);

        return back()->with('success', 'Catatan pelanggaran berhasil ditambahkan.');
    }

    public function update(Request $request, Violation $violation): RedirectResponse
    {
        $this->authorize('violation.update');

        $validated = $request->validate([
            'violation_category_id' => 'required|integer|exists:violation_categories,id',
            'violation_date' => 'required|date',
            'points' => 'required|integer|min:1|max:100',
            'description' => 'nullable|string|max:500',
            'sanction_given' => 'nullable|string|max:500',
            'status' => 'required|in:recorded,resolved,follow_up_needed',
            'follow_up_notes' => 'nullable|string|max:500',
        ]);

        $this->violationRepository->update($violation, $validated);

        return back()->with('success', 'Catatan pelanggaran berhasil diperbarui.');
    }

    public function destroy(Violation $violation): RedirectResponse
    {
        $this->authorize('violation.delete');

        $this->violationRepository->delete($violation);

        return back()->with('success', 'Catatan pelanggaran berhasil dihapus.');
    }
}

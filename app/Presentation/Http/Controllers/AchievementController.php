<?php

namespace App\Presentation\Http\Controllers;

use App\Domain\Achievement\Repositories\AchievementRepositoryInterface;
use App\Domain\SchoolClass\Repositories\SchoolClassRepositoryInterface;
use App\Domain\Student\Repositories\StudentRepositoryInterface;
use App\Models\Achievement;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class AchievementController extends Controller
{
    public function __construct(
        private readonly AchievementRepositoryInterface $achievementRepository,
        private readonly StudentRepositoryInterface $studentRepository,
        private readonly SchoolClassRepositoryInterface $classRepository,
    ) {}

    public function index(Request $request): Response
    {
        $this->authorize('achievement.view');

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

        // Student: only own achievements
        if ($user->hasRole('Siswa') && ! $user->isSuperAdmin()) {
            $student = $this->studentRepository->findByUserId($user->id);
            $studentId = $student?->id;
        }

        $achievements = $this->achievementRepository->paginate(
            perPage: 15,
            studentId: $request->query('student_id') ? (int) $request->query('student_id') : $studentId,
            classId: $request->query('class_id') ? (int) $request->query('class_id') : $classId,
            categoryId: $request->query('category_id') ? (int) $request->query('category_id') : null,
            search: $request->query('search'),
            allowedClassIds: $allowedClassIds
        );

        $studentsQuery = Student::with('schoolClass')
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

        return Inertia::render('Achievements/Index', [
            'achievements' => $achievements->through(fn ($a) => [
                'id' => $a->id,
                'student_name' => $a->student?->name,
                'student_nis' => $a->student?->nis,
                'class_name' => $a->student?->schoolClass?->name,
                'title' => $a->title,
                'category' => $a->category?->name,
                'level' => $a->level,
                'rank' => $a->rank,
                'points' => $a->points,
                'event_date' => $a->event_date?->toDateString(),
                'verification_status' => $a->verification_status,
                'recorded_by' => $a->recordedBy?->name,
            ]),
            'filters' => $request->only('search', 'class_id', 'category_id', 'student_id'),
            'categories' => $this->achievementRepository->getActiveCategories()->map(fn ($c) => ['id' => $c->id, 'name' => $c->name]),
            'classes' => $classes->map(fn ($c) => ['id' => $c->id, 'name' => $c->name]),
            'students' => $students,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('achievement.create');

        $user = Auth::user();

        $validated = $request->validate([
            'student_id' => 'required|integer|exists:students,id',
            'achievement_category_id' => 'required|integer|exists:achievement_categories,id',
            'title' => 'required|string|max:255',
            'level' => 'required|in:Sekolah,Kecamatan,Kabupaten/Kota,Provinsi,Nasional,Internasional',
            'rank' => 'nullable|string|max:50',
            'points' => 'required|integer|min:1',
            'event_date' => 'required|date',
            'organizer' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'verification_status' => 'required|in:pending,verified,rejected',
        ]);

        if ($user->isWaliKelasOnly()) {
            $allowedClassIds = $user->getHomeroomClassIds();
            $targetStudent = Student::find($validated['student_id']);
            if (! $targetStudent || ! in_array($targetStudent->class_id, $allowedClassIds, false)) {
                abort(403, 'Anda hanya dapat mencatat prestasi untuk siswa di kelas yang Anda walikan.');
            }
        }

        $validated['recorded_by_user_id'] = Auth::id();
        $this->achievementRepository->create($validated);

        return back()->with('success', 'Catatan prestasi berhasil ditambahkan.');
    }

    public function update(Request $request, Achievement $achievement): RedirectResponse
    {
        $this->authorize('achievement.update');

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'achievement_category_id' => 'required|integer|exists:achievement_categories,id',
            'level' => 'required|in:Sekolah,Kecamatan,Kabupaten/Kota,Provinsi,Nasional,Internasional',
            'rank' => 'nullable|string|max:50',
            'points' => 'required|integer|min:1',
            'event_date' => 'required|date',
            'organizer' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'verification_status' => 'required|in:pending,verified,rejected',
        ]);

        $this->achievementRepository->update($achievement, $validated);

        return back()->with('success', 'Catatan prestasi berhasil diperbarui.');
    }

    public function destroy(Achievement $achievement): RedirectResponse
    {
        $this->authorize('achievement.delete');

        $this->achievementRepository->delete($achievement);

        return back()->with('success', 'Catatan prestasi berhasil dihapus.');
    }
}

<?php

namespace App\Presentation\Http\Controllers;

use App\Domain\Achievement\Repositories\AchievementRepositoryInterface;
use App\Domain\Counseling\Repositories\CounselingRepositoryInterface;
use App\Domain\Student\Repositories\StudentRepositoryInterface;
use App\Domain\Violation\Repositories\ViolationRepositoryInterface;
use App\Models\AcademicYear;
use App\Models\AuditLog;
use App\Models\CounselingRequest;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(
        private readonly StudentRepositoryInterface $studentRepository,
        private readonly CounselingRepositoryInterface $counselingRepository,
        private readonly ViolationRepositoryInterface $violationRepository,
        private readonly AchievementRepositoryInterface $achievementRepository,
    ) {}

    public function index(): Response
    {
        $this->authorize('dashboard.view');

        $user = Auth::user();

        if ($user->hasRole('Siswa') && !$user->isSuperAdmin()) {
            return $this->studentDashboard($user);
        }

        if ($user->hasRole('Wali Kelas') && !$user->isSuperAdmin()) {
            return $this->waliKelasDashboard($user);
        }

        if ($user->isSuperAdmin() || $user->is_system_user) {
            return $this->superadminDashboard($user);
        }

        if ($user->hasRole('Guru BK')) {
            return $this->guruBkDashboard($user);
        }

        if ($user->hasRole('Kepala Sekolah')) {
            return $this->kepalaSekolahDashboard($user);
        }

        return Inertia::render('Dashboard', ['stats' => [], 'counselingSchedules' => []]);
    }

    private function studentDashboard($user): Response
    {
        $student = $this->studentRepository->findByUserId($user->id);

        if (!$student) {
            return Inertia::render('Dashboard', [
                'stats' => [],
                'role' => 'siswa',
                'counselingSchedules' => [],
                'scheduleScope' => 'Jadwal Konseling Saya',
            ]);
        }

        $recentRequests = CounselingRequest::where('student_id', $student->id)
            ->with('counselingType')
            ->latest()
            ->take(5)
            ->get()
            ->map(fn ($r) => [
                'id' => $r->id,
                'type' => $r->counselingType?->name,
                'status' => $r->status,
                'preferred_date' => $r->preferred_date?->toDateString(),
                'scheduled_datetime' => $r->scheduled_datetime?->toDateTimeString(),
            ]);

        $sessions = \App\Models\CounselingSession::where('student_id', $student->id)
            ->with(['student.schoolClass', 'counselor', 'counselingType'])
            ->orderBy('session_date', 'asc')
            ->get();

        return Inertia::render('Dashboard', [
            'role' => 'siswa',
            'student' => [
                'name' => $student->name,
                'nis' => $student->nis,
                'class_name' => $student->schoolClass?->name,
                'total_violation_points' => $student->total_violation_points,
                'total_achievement_points' => $student->total_achievement_points,
            ],
            'recentRequests' => $recentRequests,
            'counselingSchedules' => $this->formatSchedules($sessions),
            'scheduleScope' => 'Jadwal Konseling Saya',
            'stats' => [
                'pending_requests' => CounselingRequest::where('student_id', $student->id)->where('status', 'pending')->count(),
                'today_sessions' => \App\Models\CounselingSession::where('student_id', $student->id)
                    ->where('status', 'scheduled')
                    ->whereDate('session_date', today())
                    ->count(),
                'total_counseling' => CounselingRequest::where('student_id', $student->id)->where('status', 'completed')->count(),
                'total_violations' => $student->total_violation_points,
                'total_achievements' => $student->total_achievement_points,
            ],
        ]);
    }

    private function waliKelasDashboard($user): Response
    {
        $homeroomClassIds = $user->getHomeroomClassIds();

        if (empty($homeroomClassIds)) {
            return Inertia::render('Dashboard', [
                'stats' => [],
                'role' => 'wali_kelas',
                'counselingSchedules' => [],
                'scheduleScope' => 'Jadwal Siswa Kelas (Belum Ada Kelas yang Di-walikan)',
            ]);
        }

        $classes = \App\Models\SchoolClass::whereIn('id', $homeroomClassIds)->get();
        $classNames = $classes->pluck('name')->join(', ');

        $students = \App\Models\Student::whereIn('class_id', $homeroomClassIds)->get();
        $studentIds = $students->pluck('id');

        $sessions = \App\Models\CounselingSession::whereIn('student_id', $studentIds)
            ->with(['student.schoolClass', 'counselor', 'counselingType'])
            ->orderBy('session_date', 'asc')
            ->get();

        $recentRequests = CounselingRequest::whereIn('student_id', $studentIds)
            ->with(['student.schoolClass', 'counselingType'])
            ->latest()
            ->take(6)
            ->get()
            ->map(fn ($r) => [
                'id' => $r->id,
                'student_name' => $r->student?->name,
                'class_name' => $r->student?->schoolClass?->name,
                'type' => $r->counselingType?->name,
                'status' => $r->status,
                'preferred_date' => $r->preferred_date?->toDateString(),
            ]);

        return Inertia::render('Dashboard', [
            'role' => 'wali_kelas',
            'class' => ['id' => $classes->first()?->id, 'name' => $classNames],
            'classes' => $classes->map(fn ($c) => ['id' => $c->id, 'name' => $c->name]),
            'counselingSchedules' => $this->formatSchedules($sessions),
            'scheduleScope' => "Jadwal Siswa Binaan ({$classNames})",
            'recentRequests' => $recentRequests,
            'stats' => [
                'total_students' => $students->count(),
                'pending_counseling' => CounselingRequest::whereIn('student_id', $studentIds)->where('status', 'pending')->count(),
                'today_sessions' => \App\Models\CounselingSession::whereIn('student_id', $studentIds)
                    ->where('status', 'scheduled')
                    ->whereDate('session_date', today())
                    ->count(),
                'total_violations' => \App\Models\Violation::whereIn('student_id', $studentIds)->sum('points'),
                'total_achievements' => \App\Models\Achievement::whereIn('student_id', $studentIds)->where('verification_status', 'verified')->count(),
            ],
            'students' => $students->take(8)->map(fn ($s) => [
                'id' => $s->id,
                'name' => $s->name,
                'nis' => $s->nis,
                'total_violation_points' => $s->total_violation_points,
                'total_achievement_points' => $s->total_achievement_points,
            ]),
        ]);
    }

    private function superadminDashboard($user): Response
    {
        $sessions = \App\Models\CounselingSession::with(['student.schoolClass', 'counselor', 'counselingType'])
            ->orderBy('session_date', 'asc')
            ->get();

        $recentRequests = CounselingRequest::with(['student.schoolClass', 'counselingType'])
            ->whereIn('status', ['pending', 'approved'])
            ->latest()
            ->take(6)
            ->get()
            ->map(fn ($r) => [
                'id' => $r->id,
                'student_name' => $r->student?->name,
                'class_name' => $r->student?->schoolClass?->name,
                'type' => $r->counselingType?->name,
                'status' => $r->status,
                'preferred_date' => $r->preferred_date?->toDateString(),
            ]);

        return Inertia::render('Dashboard', [
            'role' => 'superadmin',
            'counselingSchedules' => $this->formatSchedules($sessions),
            'scheduleScope' => 'Seluruh Jadwal Sekolah (Akses Penuh Superadmin)',
            'stats' => [
                'pending_requests' => CounselingRequest::where('status', 'pending')->count(),
                'today_sessions' => \App\Models\CounselingSession::where('status', 'scheduled')
                    ->whereDate('session_date', today())
                    ->count(),
                'total_students' => Student::where('status', 'active')->count(),
                'total_counseling_this_month' => CounselingRequest::where('status', 'completed')
                    ->whereMonth('updated_at', now()->month)
                    ->count(),
                'total_violations' => \App\Models\Violation::count(),
                'total_achievements' => \App\Models\Achievement::where('verification_status', 'verified')->count(),
            ],
            'recentRequests' => $recentRequests,
        ]);
    }

    private function guruBkDashboard($user): Response
    {
        $sessions = \App\Models\CounselingSession::with(['student.schoolClass', 'counselor', 'counselingType'])
            ->orderBy('session_date', 'asc')
            ->get();

        $recentRequests = CounselingRequest::with(['student.schoolClass', 'counselingType'])
            ->whereIn('status', ['pending', 'approved'])
            ->latest()
            ->take(6)
            ->get()
            ->map(fn ($r) => [
                'id' => $r->id,
                'student_name' => $r->student?->name,
                'class_name' => $r->student?->schoolClass?->name,
                'type' => $r->counselingType?->name,
                'status' => $r->status,
                'preferred_date' => $r->preferred_date?->toDateString(),
            ]);

        return Inertia::render('Dashboard', [
            'role' => 'guru_bk',
            'counselingSchedules' => $this->formatSchedules($sessions),
            'scheduleScope' => 'Jadwal Bimbingan Konseling (Guru BK)',
            'stats' => [
                'pending_requests' => CounselingRequest::where('status', 'pending')->count(),
                'today_sessions' => \App\Models\CounselingSession::where('status', 'scheduled')
                    ->whereDate('session_date', today())
                    ->count(),
                'total_students' => Student::where('status', 'active')->count(),
                'total_counseling_this_month' => CounselingRequest::where('status', 'completed')
                    ->whereMonth('updated_at', now()->month)
                    ->count(),
            ],
            'recentRequests' => $recentRequests,
        ]);
    }

    private function kepalaSekolahDashboard($user): Response
    {
        $sessions = \App\Models\CounselingSession::with(['student.schoolClass', 'counselor', 'counselingType'])
            ->orderBy('session_date', 'asc')
            ->get();

        return Inertia::render('Dashboard', [
            'role' => 'kepala_sekolah',
            'counselingSchedules' => $this->formatSchedules($sessions),
            'scheduleScope' => 'Monitoring Seluruh Jadwal Sekolah',
            'stats' => [
                'total_students' => Student::where('status', 'active')->count(),
                'today_sessions' => \App\Models\CounselingSession::where('status', 'scheduled')
                    ->whereDate('session_date', today())
                    ->count(),
                'total_counseling' => CounselingRequest::where('status', 'completed')->count(),
                'total_violations' => \App\Models\Violation::count(),
                'total_achievements' => \App\Models\Achievement::where('verification_status', 'verified')->count(),
            ],
        ]);
    }

    private function formatSchedules($sessions): array
    {
        return $sessions->map(function ($s) {
            return [
                'id' => $s->id,
                'request_id' => $s->counseling_request_id,
                'session_date' => $s->session_date?->toIso8601String(),
                'date' => $s->session_date?->toDateString(),
                'time' => $s->session_date?->format('H:i') ?? '09:00',
                'formatted_date' => $s->session_date?->translatedFormat('d M Y') ?? ($s->session_date?->format('d M Y') ?? '-'),
                'student_name' => $s->student?->name ?? 'Siswa',
                'student_nis' => $s->student?->nis ?? '-',
                'class_name' => $s->student?->schoolClass?->name ?? '-',
                'counselor_name' => $s->counselor?->name ?? 'Guru BK',
                'counseling_type' => $s->counselingType?->name ?? 'Konseling Umum',
                'venue' => $s->venue ?? 'Ruang BK',
                'status' => $s->status ?? 'scheduled',
            ];
        })->values()->all();
    }
}

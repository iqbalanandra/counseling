<?php

namespace App\Presentation\Http\Controllers;

use App\Application\UseCases\Counseling\ApproveCounselingRequestUseCase;
use App\Application\UseCases\Counseling\RecordCounselingSessionUseCase;
use App\Application\UseCases\Counseling\SubmitCounselingRequestUseCase;
use App\Domain\Counseling\Repositories\CounselingRepositoryInterface;
use App\Domain\Student\Repositories\StudentRepositoryInterface;
use App\Models\CounselingRequest;
use App\Models\CounselingSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class CounselingController extends Controller
{
    public function __construct(
        private readonly CounselingRepositoryInterface $counselingRepository,
        private readonly StudentRepositoryInterface $studentRepository,
        private readonly SubmitCounselingRequestUseCase $submitRequest,
        private readonly ApproveCounselingRequestUseCase $approveRequest,
        private readonly RecordCounselingSessionUseCase $recordSession,
    ) {}

    public function requestsIndex(Request $request): Response
    {
        $this->authorize('counseling.view');

        $user = Auth::user();
        $studentId = null;
        $allowedClassIds = null;

        if ($user->isWaliKelasOnly()) {
            $allowedClassIds = $user->getHomeroomClassIds();
        }

        // Students can only see their own requests
        if ($user->hasRole('Siswa') && !$user->isSuperAdmin()) {
            $student = $this->studentRepository->findByUserId($user->id);
            $studentId = $student?->id;
        }

        $requests = $this->counselingRepository->paginateRequests(
            perPage: 15,
            studentId: $studentId,
            status: $request->query('status'),
            allowedClassIds: $allowedClassIds
        );

        return Inertia::render('Counseling/Requests/Index', [
            'requests' => $requests->through(fn ($r) => [
                'id' => $r->id,
                'student_name' => $r->student?->name,
                'student_nis' => $r->student?->nis,
                'class_name' => $r->student?->schoolClass?->name,
                'counseling_type' => $r->counselingType?->name,
                'preferred_date' => $r->preferred_date?->toDateString(),
                'preferred_time' => $r->preferred_time,
                'scheduled_datetime' => $r->scheduled_datetime?->toDateTimeString(),
                'status' => $r->status,
                'counselor_name' => $r->counselor?->name,
                'created_at' => $r->created_at?->toDateTimeString(),
            ]),
            'filters' => $request->only('status'),
            'types' => $this->counselingRepository->getActiveTypes()->map(fn ($t) => ['id' => $t->id, 'name' => $t->name]),
        ]);
    }

    public function showRequest(CounselingRequest $counselingRequest): Response
    {
        $this->authorize('counseling.view');

        $counselingRequest->load(['student.schoolClass', 'counselingType', 'counselor', 'session.record']);

        $user = Auth::user();
        if ($user->isWaliKelasOnly()) {
            $allowedClassIds = $user->getHomeroomClassIds();
            if (!in_array($counselingRequest->student?->class_id, $allowedClassIds)) {
                abort(403, 'Anda tidak memiliki akses ke permohonan konseling siswa di luar kelas perwalian Anda.');
            }
        }

        $canViewPrivate = $user->hasPermission('counseling.view_private');

        $data = [
            'id' => $counselingRequest->id,
            'student_name' => $counselingRequest->student?->name,
            'student_nis' => $counselingRequest->student?->nis,
            'class_name' => $counselingRequest->student?->schoolClass?->name,
            'counseling_type' => $counselingRequest->counselingType?->name,
            'preferred_date' => $counselingRequest->preferred_date?->toDateString(),
            'preferred_time' => $counselingRequest->preferred_time,
            'scheduled_datetime' => $counselingRequest->scheduled_datetime?->toDateTimeString(),
            'status' => $counselingRequest->status,
            'counselor_name' => $counselingRequest->counselor?->name,
            'notes' => $counselingRequest->notes,
            'rescheduled_reason' => $counselingRequest->rescheduled_reason,
            'rejection_reason' => $counselingRequest->rejection_reason,
        ];

        // Only show confidential complaint to BK or superadmin
        if ($canViewPrivate) {
            $data['complaint_summary'] = $counselingRequest->complaint_summary;
        }

        $counselors = \App\Models\User::whereHas('roles', fn ($q) => $q->whereIn('name', ['Guru BK', 'Superadmin']))
            ->where('status', 'active')
            ->get()
            ->map(fn ($u) => [
                'id' => $u->id,
                'name' => $u->name,
                'identifier' => $u->identifier_number ?? 'Konselor',
                'badge' => $u->roles->first()?->display_name ?? 'Guru BK',
            ]);

        return Inertia::render('Counseling/Requests/Show', [
            'request' => $data,
            'canApprove' => $user->hasPermission('counseling.approve'),
            'canReschedule' => $user->hasPermission('counseling.reschedule'),
            'canViewPrivate' => $canViewPrivate,
            'types' => $this->counselingRepository->getActiveTypes()->map(fn ($t) => ['id' => $t->id, 'name' => $t->name]),
            'counselors' => $counselors,
        ]);
    }

    public function submitRequest(Request $request): RedirectResponse
    {
        $this->authorize('counseling.create');

        $validated = $request->validate([
            'counseling_type_id' => 'required|integer|exists:counseling_types,id',
            'preferred_date' => 'required|date|after:today',
            'preferred_time' => 'nullable|string|max:50',
            'complaint_summary' => 'required|string|min:20|max:2000',
        ]);

        $user = Auth::user();
        $student = $this->studentRepository->findByUserId($user->id);

        if (!$student) {
            return back()->with('error', 'Profil siswa Anda tidak ditemukan. Hubungi administrator.');
        }

        $this->submitRequest->execute($student, $validated);

        return back()->with('success', 'Permohonan konseling berhasil diajukan. Tunggu konfirmasi dari Guru BK.');
    }

    public function approveRequest(Request $request, CounselingRequest $counselingRequest): RedirectResponse
    {
        $this->authorize('counseling.approve');

        $validated = $request->validate([
            'counselor_id' => 'nullable|integer|exists:users,id',
            'scheduled_datetime' => 'required|date',
            'venue' => 'nullable|string|max:200',
            'notes' => 'nullable|string|max:500',
        ]);

        $counselor = Auth::user();
        if (!empty($validated['counselor_id'])) {
            $assigned = \App\Models\User::find($validated['counselor_id']);
            if ($assigned) {
                $counselor = $assigned;
            }
        }

        $this->approveRequest->execute($counselingRequest, $counselor, $validated);

        return back()->with('success', 'Permohonan konseling telah disetujui dan sesi dijadwalkan.');
    }

    public function rescheduleRequest(Request $request, CounselingRequest $counselingRequest): RedirectResponse
    {
        $this->authorize('counseling.reschedule');

        $validated = $request->validate([
            'scheduled_datetime' => 'required|date',
            'rescheduled_reason' => 'required|string|max:500',
        ]);

        $this->counselingRepository->updateRequestStatus($counselingRequest, 'rescheduled', [
            'scheduled_datetime' => $validated['scheduled_datetime'],
            'rescheduled_reason' => $validated['rescheduled_reason'],
        ]);

        // Notify student
        if ($counselingRequest->student?->user_id) {
            \App\Infrastructure\Services\NotificationService::send(
                userId: $counselingRequest->student->user_id,
                title: 'Jadwal Konseling Diubah',
                message: "Jadwal konseling Anda telah diubah ke {$validated['scheduled_datetime']}. Alasan: {$validated['rescheduled_reason']}",
                type: 'counseling',
                link: '/counseling/my-requests'
            );
        }

        return back()->with('success', 'Jadwal konseling berhasil diubah dan notifikasi dikirim ke siswa.');
    }

    public function sessionsIndex(Request $request): Response
    {
        $this->authorize('counseling.view');

        $user = Auth::user();
        $studentId = null;
        $counselorId = null;
        $allowedClassIds = null;

        if ($user->isWaliKelasOnly()) {
            $allowedClassIds = $user->getHomeroomClassIds();
        }

        if ($user->hasRole('Siswa') && !$user->isSuperAdmin()) {
            $student = $this->studentRepository->findByUserId($user->id);
            $studentId = $student?->id;
        }

        if ($user->hasRole('Guru BK') && !$user->isSuperAdmin()) {
            $counselorId = $user->id;
        }

        $sessions = $this->counselingRepository->paginateSessions(
            perPage: 15,
            studentId: $studentId,
            counselorId: $counselorId,
            allowedClassIds: $allowedClassIds
        );

        return Inertia::render('Counseling/Sessions/Index', [
            'sessions' => $sessions->through(fn ($s) => [
                'id' => $s->id,
                'student_name' => $s->student?->name,
                'student_nis' => $s->student?->nis,
                'class_name' => $s->student?->schoolClass?->name,
                'counseling_type' => $s->counselingType?->name,
                'session_date' => $s->session_date?->toDateTimeString(),
                'venue' => $s->venue,
                'status' => $s->status,
                'counselor_name' => $s->counselor?->name,
                'has_record' => $s->record !== null,
            ]),
        ]);
    }

    public function recordSession(Request $request, CounselingSession $counselingSession): RedirectResponse
    {
        $this->authorize('counseling.record');

        $validated = $request->validate([
            'topic' => 'required|string|max:255',
            'problem_description' => 'required|string|min:20',
            'counseling_notes' => 'required|string|min:20',
            'assessment_diagnosis' => 'nullable|string',
            'recommendations' => 'nullable|string',
            'follow_up_plan' => 'nullable|string',
        ]);

        $this->recordSession->execute($counselingSession, Auth::user(), $validated);

        return back()->with('success', 'Rekam hasil konseling berhasil disimpan.');
    }
}

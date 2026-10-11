<?php

namespace App\Application\UseCases\Counseling;

use App\Domain\Counseling\Repositories\CounselingRepositoryInterface;
use App\Infrastructure\Services\AuditLogService;
use App\Infrastructure\Services\NotificationService;
use App\Models\CounselingRequest;
use App\Models\User;

class ApproveCounselingRequestUseCase
{
    public function __construct(
        private readonly CounselingRepositoryInterface $counselingRepository
    ) {}

    public function execute(CounselingRequest $request, User $counselor, array $data): bool
    {
        $extra = [
            'counselor_id' => $counselor->id,
            'scheduled_datetime' => $data['scheduled_datetime'],
            'notes' => $data['notes'] ?? null,
        ];

        $updated = $this->counselingRepository->updateRequestStatus($request, 'approved', $extra);

        if ($updated) {
            // Create session
            $this->counselingRepository->createSession([
                'counseling_request_id' => $request->id,
                'student_id' => $request->student_id,
                'counselor_id' => $counselor->id,
                'counseling_type_id' => $request->counseling_type_id,
                'session_date' => $data['scheduled_datetime'],
                'venue' => $data['venue'] ?? 'Ruang Konseling BK',
                'status' => 'scheduled',
            ]);

            // Notify student
            if ($request->student?->user_id) {
                NotificationService::send(
                    userId: $request->student->user_id,
                    title: 'Permohonan Konseling Disetujui',
                    message: "Permohonan konseling Anda telah disetujui. Jadwal: {$data['scheduled_datetime']}.",
                    type: 'counseling',
                    link: '/counseling/my-requests'
                );
            }

            AuditLogService::log(
                action: 'approve',
                module: 'counseling',
                description: "Menyetujui permohonan konseling #{$request->id}",
                resourceId: (string) $request->id,
                newValues: ['status' => 'approved', 'scheduled_datetime' => $data['scheduled_datetime']]
            );
        }

        return $updated;
    }
}

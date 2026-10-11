<?php

namespace App\Application\UseCases\Counseling;

use App\Domain\Counseling\Repositories\CounselingRepositoryInterface;
use App\Infrastructure\Services\AuditLogService;
use App\Infrastructure\Services\NotificationService;
use App\Models\CounselingRecord;
use App\Models\CounselingSession;
use App\Models\User;

class RecordCounselingSessionUseCase
{
    public function __construct(
        private readonly CounselingRepositoryInterface $counselingRepository
    ) {}

    public function execute(CounselingSession $session, User $counselor, array $data): CounselingRecord
    {
        // Update session status
        $session->update(['status' => 'completed']);

        // If there's a linked request, update it too
        if ($session->counseling_request_id) {
            $session->request?->update(['status' => 'completed']);
        }

        $record = $this->counselingRepository->createRecord([
            'counseling_session_id' => $session->id,
            'student_id' => $session->student_id,
            'counselor_id' => $counselor->id,
            'topic' => $data['topic'],
            'problem_description' => $data['problem_description'],
            'counseling_notes' => $data['counseling_notes'],
            'assessment_diagnosis' => $data['assessment_diagnosis'] ?? null,
            'recommendations' => $data['recommendations'] ?? null,
            'follow_up_plan' => $data['follow_up_plan'] ?? null,
            'is_confidential' => true,
        ]);

        // Notify student about follow-up if exists
        if (! empty($data['follow_up_plan']) && $session->student?->user_id) {
            NotificationService::send(
                userId: $session->student->user_id,
                title: 'Tindak Lanjut Konseling',
                message: 'Guru BK telah mencatat hasil konseling Anda dan memberikan rekomendasi tindak lanjut.',
                type: 'counseling',
                link: '/counseling/history'
            );
        }

        AuditLogService::log(
            action: 'record',
            module: 'counseling',
            description: "Mencatat hasil sesi konseling #{$session->id} untuk siswa {$session->student?->name}",
            resourceId: (string) $record->id,
            newValues: ['session_id' => $session->id, 'topic' => $data['topic'], 'is_confidential' => true]
        );

        return $record;
    }
}

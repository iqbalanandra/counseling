<?php

namespace App\Application\UseCases\Counseling;

use App\Domain\Counseling\Repositories\CounselingRepositoryInterface;
use App\Infrastructure\Services\AuditLogService;
use App\Infrastructure\Services\NotificationService;
use App\Models\CounselingRequest;
use App\Models\Student;

class SubmitCounselingRequestUseCase
{
    public function __construct(
        private readonly CounselingRepositoryInterface $counselingRepository
    ) {}

    public function execute(Student $student, array $data): CounselingRequest
    {
        $request = $this->counselingRepository->createRequest([
            'student_id' => $student->id,
            'counseling_type_id' => $data['counseling_type_id'],
            'preferred_date' => $data['preferred_date'],
            'preferred_time' => $data['preferred_time'] ?? null,
            'complaint_summary' => $data['complaint_summary'],
            'status' => 'pending',
        ]);

        // Notify all Guru BK
        NotificationService::sendToRole(
            roleName: 'Guru BK',
            title: 'Permohonan Konseling Baru',
            message: "Siswa {$student->name} ({$student->nis}) mengajukan permohonan konseling.",
            type: 'counseling',
            link: '/counseling/requests'
        );

        AuditLogService::log(
            action: 'create',
            module: 'counseling',
            description: "Siswa {$student->name} mengajukan permohonan konseling",
            resourceId: (string) $request->id,
            newValues: ['student_id' => $student->id, 'counseling_type_id' => $data['counseling_type_id'], 'status' => 'pending']
        );

        return $request;
    }
}

<?php

namespace App\Domain\Counseling\Repositories;

use App\Models\CounselingRecord;
use App\Models\CounselingRequest;
use App\Models\CounselingSession;
use App\Models\CounselingType;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface CounselingRepositoryInterface
{
    public function getActiveTypes(): Collection;

    public function paginateRequests(int $perPage = 15, ?int $studentId = null, ?string $status = null, ?array $allowedClassIds = null): LengthAwarePaginator;

    public function findRequestById(int $id): ?CounselingRequest;

    public function createRequest(array $data): CounselingRequest;

    public function updateRequestStatus(CounselingRequest $request, string $status, array $extra = []): bool;

    public function createSession(array $data): CounselingSession;

    public function findSessionById(int $id): ?CounselingSession;

    public function paginateSessions(int $perPage = 15, ?int $studentId = null, ?int $counselorId = null, ?array $allowedClassIds = null): LengthAwarePaginator;

    public function createRecord(array $data): CounselingRecord;

    public function findRecordById(int $id): ?CounselingRecord;

    public function findRecordBySessionId(int $sessionId): ?CounselingRecord;

    public function updateRecord(CounselingRecord $record, array $data): bool;
}

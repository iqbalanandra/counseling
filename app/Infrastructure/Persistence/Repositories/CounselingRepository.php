<?php

namespace App\Infrastructure\Persistence\Repositories;

use App\Domain\Counseling\Repositories\CounselingRepositoryInterface;
use App\Models\CounselingRecord;
use App\Models\CounselingRequest;
use App\Models\CounselingSession;
use App\Models\CounselingType;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class CounselingRepository implements CounselingRepositoryInterface
{
    public function getActiveTypes(): Collection
    {
        return CounselingType::where('is_active', true)->get();
    }

    public function paginateRequests(int $perPage = 15, ?int $studentId = null, ?string $status = null, ?array $allowedClassIds = null): LengthAwarePaginator
    {
        return CounselingRequest::query()
            ->with(['student.schoolClass', 'counselingType', 'counselor'])
            ->when($studentId, fn (Builder $q) => $q->where('student_id', $studentId))
            ->when($status, fn (Builder $q) => $q->where('status', $status))
            ->when($allowedClassIds !== null, fn (Builder $q) => $q->whereHas('student', fn ($sq) => $sq->whereIn('class_id', $allowedClassIds)))
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function findRequestById(int $id): ?CounselingRequest
    {
        return CounselingRequest::with(['student.schoolClass', 'counselingType', 'counselor', 'session.record'])->find($id);
    }

    public function createRequest(array $data): CounselingRequest
    {
        return CounselingRequest::create($data);
    }

    public function updateRequestStatus(CounselingRequest $request, string $status, array $extra = []): bool
    {
        return $request->update(array_merge(['status' => $status], $extra));
    }

    public function createSession(array $data): CounselingSession
    {
        return CounselingSession::create($data);
    }

    public function findSessionById(int $id): ?CounselingSession
    {
        return CounselingSession::with(['student.schoolClass', 'counselor', 'counselingType', 'record'])->find($id);
    }

    public function paginateSessions(int $perPage = 15, ?int $studentId = null, ?int $counselorId = null, ?array $allowedClassIds = null): LengthAwarePaginator
    {
        return CounselingSession::query()
            ->with(['student.schoolClass', 'counselor', 'counselingType', 'record'])
            ->when($studentId, fn (Builder $q) => $q->where('student_id', $studentId))
            ->when($counselorId, fn (Builder $q) => $q->where('counselor_id', $counselorId))
            ->when($allowedClassIds !== null, fn (Builder $q) => $q->whereHas('student', fn ($sq) => $sq->whereIn('class_id', $allowedClassIds)))
            ->latest('session_date')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function createRecord(array $data): CounselingRecord
    {
        return CounselingRecord::create($data);
    }

    public function findRecordById(int $id): ?CounselingRecord
    {
        return CounselingRecord::with(['session', 'student', 'counselor'])->find($id);
    }

    public function findRecordBySessionId(int $sessionId): ?CounselingRecord
    {
        return CounselingRecord::with(['session', 'student', 'counselor'])->where('counseling_session_id', $sessionId)->first();
    }

    public function updateRecord(CounselingRecord $record, array $data): bool
    {
        return $record->update($data);
    }
}

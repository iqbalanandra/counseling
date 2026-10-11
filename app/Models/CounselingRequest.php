<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CounselingRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'counseling_type_id',
        'counselor_id',
        'preferred_date',
        'preferred_time',
        'scheduled_datetime',
        'complaint_summary',
        'status',
        'notes',
        'rescheduled_reason',
        'rejection_reason',
        'cancellation_reason',
    ];

    protected $casts = [
        'preferred_date' => 'date',
        'scheduled_datetime' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function counselingType(): BelongsTo
    {
        return $this->belongsTo(CounselingType::class);
    }

    public function counselor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'counselor_id');
    }

    public function session(): HasOne
    {
        return $this->hasOne(CounselingSession::class);
    }
}

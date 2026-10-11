<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CounselingSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'counseling_request_id',
        'student_id',
        'counselor_id',
        'counseling_type_id',
        'session_date',
        'venue',
        'status',
    ];

    protected $casts = [
        'session_date' => 'datetime',
    ];

    public function request(): BelongsTo
    {
        return $this->belongsTo(CounselingRequest::class, 'counseling_request_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function counselor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'counselor_id');
    }

    public function counselingType(): BelongsTo
    {
        return $this->belongsTo(CounselingType::class);
    }

    public function record(): HasOne
    {
        return $this->hasOne(CounselingRecord::class);
    }
}

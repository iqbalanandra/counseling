<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CounselingRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'counseling_session_id',
        'student_id',
        'counselor_id',
        'topic',
        'problem_description',
        'counseling_notes',
        'assessment_diagnosis',
        'recommendations',
        'follow_up_plan',
        'is_confidential',
    ];

    protected $casts = [
        'is_confidential' => 'boolean',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(CounselingSession::class, 'counseling_session_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function counselor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'counselor_id');
    }
}

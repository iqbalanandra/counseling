<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Achievement extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'achievement_category_id',
        'recorded_by_user_id',
        'title',
        'level',
        'rank',
        'points',
        'event_date',
        'organizer',
        'description',
        'certificate_path',
        'verification_status',
    ];

    protected $casts = [
        'event_date' => 'date',
        'points' => 'integer',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(AchievementCategory::class, 'achievement_category_id');
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by_user_id');
    }
}

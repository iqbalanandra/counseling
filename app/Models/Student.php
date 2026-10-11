<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nis',
        'nisn',
        'name',
        'gender',
        'birth_place',
        'birth_date',
        'address',
        'parent_name',
        'parent_phone',
        'class_id',
        'status',
        'total_violation_points',
        'total_achievement_points',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'total_violation_points' => 'integer',
        'total_achievement_points' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function counselingRequests(): HasMany
    {
        return $this->hasMany(CounselingRequest::class);
    }

    public function counselingSessions(): HasMany
    {
        return $this->hasMany(CounselingSession::class);
    }

    public function counselingRecords(): HasMany
    {
        return $this->hasMany(CounselingRecord::class);
    }

    public function violations(): HasMany
    {
        return $this->hasMany(Violation::class);
    }

    public function achievements(): HasMany
    {
        return $this->hasMany(Achievement::class);
    }
}

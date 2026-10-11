<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SchoolClass extends Model
{
    use HasFactory;

    protected $table = 'classes';

    protected $fillable = [
        'name',
        'grade',
        'academic_year_id',
        'homeroom_teacher_id',
    ];

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function homeroomTeacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'homeroom_teacher_id');
    }

    public function homeroomTeachers(): BelongsToMany
    {
        return $this->belongsToMany(Teacher::class, 'class_homeroom_teachers', 'class_id', 'teacher_id')->withTimestamps();
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'class_id');
    }
}

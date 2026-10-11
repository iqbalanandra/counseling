<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Collection;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nip',
        'name',
        'gender',
        'phone',
        'position',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function homeroomClass(): HasOne
    {
        return $this->hasOne(SchoolClass::class, 'homeroom_teacher_id');
    }

    public function homeroomClasses(): BelongsToMany
    {
        return $this->belongsToMany(SchoolClass::class, 'class_homeroom_teachers', 'teacher_id', 'class_id')->withTimestamps();
    }

    /**
     * Get all assigned class IDs (combining pivot and direct FK)
     *
     * @return int[]
     */
    public function getAllHomeroomClassIds(): array
    {
        $pivotIds = $this->homeroomClasses()->pluck('classes.id')->toArray();
        $directIds = SchoolClass::where('homeroom_teacher_id', $this->id)->pluck('id')->toArray();

        return array_values(array_unique(array_merge($pivotIds, $directIds)));
    }

    /**
     * Get all assigned SchoolClass instances
     */
    public function getAllHomeroomClasses(): Collection
    {
        $ids = $this->getAllHomeroomClassIds();
        if (empty($ids)) {
            return new Collection();
        }

        return SchoolClass::whereIn('id', $ids)->get();
    }
}

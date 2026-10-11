<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ViolationCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'points',
        'sanction',
        'is_active',
    ];

    protected $casts = [
        'points' => 'integer',
        'is_active' => 'boolean',
    ];

    public function violations(): HasMany
    {
        return $this->hasMany(Violation::class);
    }
}

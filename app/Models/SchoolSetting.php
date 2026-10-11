<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SchoolSetting extends Model
{
    use HasFactory;

    protected $table = 'school_settings';

    protected $fillable = [
        'school_name',
        'portal_name',
        'tagline',
        'npsn',
        'logo_url',
        'address',
        'phone',
        'whatsapp',
        'email',
        'website',
        'principal_name',
        'welcome_message',
    ];

    /**
     * Get the active school settings (cached for performance).
     */
    public static function getSettings(): array
    {
        return Cache::remember('school_settings_data', 3600, function () {
            $setting = static::first();

            if (! $setting) {
                return [
                    'school_name' => 'SMP Negeri 1 Metro',
                    'portal_name' => 'Konseling-Yu',
                    'tagline' => 'Portal Bimbingan & Konseling Terpadu',
                    'npsn' => '20104567',
                    'logo_url' => null,
                    'address' => 'Jl. Merdeka No. 45, Kebayoran Baru, Jakarta Selatan',
                    'phone' => '(021) 7654321',
                    'whatsapp' => '0812-3456-7890',
                    'email' => 'info@smpn1cerdasmandiri.sch.id',
                    'website' => 'https://smpn1cerdasmandiri.sch.id',
                    'principal_name' => 'Drs. Bambang Sutrisno, M.Pd',
                    'welcome_message' => 'Ruang aman dan nyaman untuk konsultasi, bimbingan belajar, eksplorasi masa depan, dan pendampingan karakter.',
                ];
            }

            return $setting->toArray();
        });
    }

    /**
     * Clear cached settings.
     */
    public static function clearCache(): void
    {
        Cache::forget('school_settings_data');
    }
}

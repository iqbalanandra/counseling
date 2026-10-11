<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $phone
 * @property string|null $identifier_number
 * @property string $status
 * @property bool $is_system_user
 * @property string|null $avatar_url
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'identifier_number',
        'status',
        'is_system_user',
        'avatar_url',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_system_user' => 'boolean',
        ];
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles')->withTimestamps();
    }

    public function student(): HasOne
    {
        return $this->hasOne(Student::class);
    }

    public function teacher(): HasOne
    {
        return $this->hasOne(Teacher::class);
    }

    public function appNotifications(): HasMany
    {
        return $this->hasMany(AppNotification::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    public function isSuperAdmin(): bool
    {
        if ($this->is_system_user) {
            return true;
        }

        return $this->roles->contains(function ($role) {
            return $role->name === 'Superadmin';
        });
    }

    public function hasRole(string|array $roleName): bool
    {
        if ($this->is_system_user) {
            return true;
        }

        $roles = is_array($roleName) ? $roleName : [$roleName];

        return $this->roles->contains(function ($role) use ($roles) {
            return in_array($role->name, $roles, true);
        });
    }

    /**
     * Get IDs of classes this user is homeroom teacher for
     *
     * @return int[]
     */
    public function getHomeroomClassIds(): array
    {
        if (!$this->teacher) {
            return [];
        }

        return $this->teacher->getAllHomeroomClassIds();
    }

    /**
     * Returns true if user is Wali Kelas and does not have higher administrative / counseling roles
     */
    public function isWaliKelasOnly(): bool
    {
        if ($this->isSuperAdmin() || $this->hasRole(['Superadmin', 'Kepala Sekolah', 'Guru BK'])) {
            return false;
        }

        return $this->hasRole('Wali Kelas');
    }

    public function getAllPermissions(): array
    {
        if ($this->is_system_user) {
            return Permission::pluck('name')->toArray();
        }

        return $this->roles()
            ->with('permissions')
            ->get()
            ->flatMap(fn ($role) => $role->permissions->pluck('name'))
            ->unique()
            ->values()
            ->toArray();
    }

    public function hasPermission(string $permissionName): bool
    {
        if ($this->is_system_user) {
            return true;
        }

        $permissions = $this->getAllPermissions();

        return in_array('*', $permissions, true) || in_array($permissionName, $permissions, true);
    }

    public function scopeRole($query, string|array $roleName)
    {
        $roles = is_array($roleName) ? $roleName : [$roleName];
        return $query->whereHas('roles', fn ($q) => $q->whereIn('name', $roles));
    }
}

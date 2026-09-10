<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles, CanResetPassword;

    public const STATUSES = [
        'active' => 'Active',
        'inactive' => 'Inactive',
    ];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
        'last_login_at',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class, 'engineer_id');
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function scopeEngineers(Builder $query): Builder
    {
        return $query->role('engineer');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isEngineer(): bool
    {
        return $this->hasRole('engineer');
    }

    public function isManager(): bool
    {
        return $this->hasRole('manager');
    }

    public function displayRole(): string
    {
        return match (true) {
            $this->isAdmin() => 'Administrator',
            $this->isManager() => 'Manager',
            $this->isEngineer() => 'Engineer',
            default => 'User',
        };
    }
}

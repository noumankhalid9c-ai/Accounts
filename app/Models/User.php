<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $table = 'users';
    protected $guarded = [];

    protected $casts = [
        'must_change_password' => 'boolean',
        'failed_login_attempts' => 'integer',
        'locked_until' => 'datetime',
        'last_login_at' => 'datetime',
        'password_changed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function userPermissions()
    {
        return $this->belongsToMany(Permission::class, 'user_permissions', 'user_id', 'permission_id')
            ->withPivot('is_granted');
    }

    public function loginHistories()
    {
        return $this->hasMany(LoginHistory::class, 'user_id');
    }

    public function activities()
    {
        return $this->hasMany(AuditLog::class, 'user_id');
    }

    public function isSuperAdmin(): bool
    {
        return $this->role_id === 1 || ($this->role && $this->role->slug === 'super-admin');
    }

    public function hasRole($roles): bool
    {
        if (is_array($roles)) {
            foreach ($roles as $role) {
                if ($this->hasRole($role)) {
                    return true;
                }
            }
            return false;
        }

        if (is_numeric($roles)) {
            return $this->role_id == $roles;
        }

        if ($this->role) {
            return strtolower($this->role->slug) === strtolower($roles)
                || strtolower($this->role->name) === strtolower($roles);
        }

        return false;
    }

    public function hasPermission(string $permissionSlug): bool
    {
        // 1. Super Admin bypasses all checks
        if ($this->isSuperAdmin()) {
            return true;
        }

        // 2. Direct user override
        $userPerms = $this->relationLoaded('userPermissions') ? $this->userPermissions : $this->userPermissions()->get();
        $direct = $userPerms->firstWhere('slug', $permissionSlug);
        if ($direct) {
            return (bool)$direct->pivot->is_granted;
        }

        // 3. Fall back to Role permissions
        if ($this->role) {
            return $this->role->hasPermission($permissionSlug);
        }

        return false;
    }

    public function canDo(string $permissionSlug): bool
    {
        return $this->hasPermission($permissionSlug);
    }

    public function isLocked(): bool
    {
        if ($this->status === 'Locked') {
            return true;
        }

        if ($this->locked_until && $this->locked_until->isFuture()) {
            return true;
        }

        return false;
    }

    public function isActive(): bool
    {
        return $this->status === 'Active' && !$this->isLocked();
    }

    public function getRoleNameAttribute(): string
    {
        return $this->role?->name ?? 'User';
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->profile_picture && file_exists(public_path('uploads/avatars/' . $this->profile_picture))) {
            return asset('uploads/avatars/' . $this->profile_picture);
        }

        // Beautiful initial fallback SVG / data URI or standard placeholder
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=4f46e5&color=fff&bold=true';
    }

    public function getLandingUrl(): string
    {

        if ($this->isSuperAdmin() || $this->canDo('dashboard.view')) {
            return route('dashboard');
        }

        if ($this->canDo('crm.view')) {
            return route('crm.dashboard');
        }

        if ($this->canDo('clients.view')) {
            return route('clients.index');
        }

        if ($this->canDo('airline_tickets.view')) {
            return route('airline-tickets.index');
        }

        if ($this->canDo('travel_groups.view')) {
            return route('travel-groups.index');
        }

        if ($this->canDo('b2b.view')) {
            return route('b2b.dashboard');
        }

        if ($this->canDo('monthly_expenses.view')) {
            return route('monthly_expenses.dashboard');
        }

        if ($this->canDo('petty_cash.view')) {
            return route('petty_cash.dashboard');
        }

        if ($this->canDo('salary.view')) {
            return route('salary.index');
        }

        if ($this->canDo('users.view')) {
            return route('users.index');
        }

        if ($this->canDo('roles.view')) {
            return route('roles.index');
        }

        if ($this->canDo('activity_logs.view')) {
            return route('activity-logs.index');
        }

        if ($this->canDo('login_history.view')) {
            return route('login-history.index');
        }

        return route('profile');
    }
}


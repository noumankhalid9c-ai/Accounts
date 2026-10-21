<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $table = 'roles';
    protected $guarded = [];

    public function users()
    {
        return $this->hasMany(User::class, 'role_id');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions', 'role_id', 'permission_id');
    }

    public function hasPermission(string $permissionSlug): bool
    {
        if ($this->slug === 'super-admin' || $this->id === 1) {
            return true;
        }

        $perms = $this->relationLoaded('permissions') ? $this->permissions : $this->permissions()->get();
        return $perms->contains('slug', $permissionSlug);
    }

    public function __toString()
    {
        return (string) ($this->name ?? $this->slug ?? '');
    }
}


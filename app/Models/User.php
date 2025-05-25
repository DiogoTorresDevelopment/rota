<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'status'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'status' => 'boolean',
    ];

    public function permissionGroups()
    {
        return $this->belongsToMany(PermissionGroup::class, 'user_has_permission_groups');
    }

    public function hasPermission($permissionSlug)
    {
        return $this->permissionGroups()
            ->whereHas('permissions', function ($query) use ($permissionSlug) {
                $query->where('slug', $permissionSlug);
            })
            ->exists();
    }

    public function getAllPermissions()
    {
        return $this->permissionGroups()
            ->with('permissions')
            ->get()
            ->pluck('permissions')
            ->flatten()
            ->unique('id');
    }
} 
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PermissionGroup extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'status'];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_group_has_permissions');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
} 
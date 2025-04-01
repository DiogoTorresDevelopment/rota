<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'slug', 'description', 'type'];

    public function permissionGroups()
    {
        return $this->belongsToMany(PermissionGroup::class, 'permission_group_has_permissions');
    }
} 
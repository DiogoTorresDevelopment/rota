<?php

use Illuminate\Support\Facades\Auth;

if (!function_exists('hasPermission')) {
    function hasPermission($permission)
    {
        if (!Auth::check()) {
            return false;
        }

        $userPermissions = session('user_permissions', []);
        return in_array($permission, $userPermissions);
    }
} 
<?php

namespace App\Models;

use App\Enums\TeamPermission;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Roles extends Model
{
    public function userRole(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class, 
            'user_roles', 
            'role_id', 
            'user_id'
        )->withTimestamps();
    }
    public function employmentRole(): BelongsToMany   
    {
        return $this->belongsToMany(
            Employments::class,
            'employment_roles',
            'role_id',
            'employment_id'
        )->withTimestamps();
    }

    public function rolePermissions(): BelongsToMany
    {
        return $this->belongsToMany(
            Permissions::class, 
            'role_permission', 
            'roles_id',
            'permission_id'
            );
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permissions extends Model
{
    public function rolePermissions(): BelongsToMany
    {
        return $this->belongsToMany(
            Roles::class, 
            'role_permission', 
            'permission_id',
            'role_id'
            );
    }
}

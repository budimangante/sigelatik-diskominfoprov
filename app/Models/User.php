<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'username',
        'email',
        'nip',
        'phone',
        'user_type',
        'organization_name',
        'password',
        'is_active',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at'     => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
        ];
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            Roles::class, 
            'user_roles', 
            'user_id', 
            'role_id'
        );
    }

    public function userIdentities(): HasMany
    {
        return $this->hasMany(UserIdentities::class, 'user_id');
    }

    public function employments(): HasMany
    {
        return $this->hasMany(Employments::class, 'user_id');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Tickets::class, 'user_id');
    }

    public function ticketMessages(): HasMany
    {
        return $this->hasMany(TicketMessage::class, 'user_id');
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLogs::class, 'user_id');
    }

    public function hasRole(string|array $roles): bool
    {
        $roles = (array) $roles;
        return $this->roles->pluck('name')->intersect($roles)->isNotEmpty();
    }
}
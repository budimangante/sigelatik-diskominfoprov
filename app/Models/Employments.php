<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employments extends Model
{
    protected $table = 'employments';
    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ticket(): HasMany
    {
        return $this->hasMany(Tickets::class, 'employment_id');
    }

    public function bidang() : BelongsTo
    {
        return $this->belongsTo(Bidang::class, 'bidang_id');
    }

    public function perangkatDaerah(): BelongsTo
    {
        return $this->belongsTo(PerangkatDaerah::class, 'perangkat_daerah_id');
    }

    public function ticketTaskAssignees() : HasMany
    {
        return $this->hasMany(TicketTaskAssignees::class, 'employment_id');
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            Roles::class,
            'employment_roles',
            'employment_id',
            'role_id'
        )->withTimestamps();
    }
}

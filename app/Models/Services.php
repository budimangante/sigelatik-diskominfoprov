<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Tickets;
use App\Models\TicketTask;

class Services extends Model
{
    protected $table = 'services';

    public function tickets(): HasMany
    {
        return $this->hasMany(Tickets::class, 'service_id');
    }

    public function ticketTasks(): HasMany
    {
        return $this->hasMany(TicketTask::class, 'service_id');
    }

    public function serviceCategories(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id');
    }

    public function serviceWorkHours(): HasMany
    {
        return $this->hasMany(WorkHours::class, 'service_id');
    }

    public function serviceActivites(): HasMany
    {
        return $this->hasMany(ServiceActivites::class, 'service_id');
    }

    public function bidang(): BelongsTo
    {
        return $this->belongsTo(Bidang::class, 'bidang_id');
    }


    public function sops():HasMany
    {
        return $this->hasMany(Sops::class, 'service_id');
    }


}
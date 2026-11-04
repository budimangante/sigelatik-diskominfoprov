<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Tickets;
use App\Models\TicketTask;
use App\Models\ServiceCategory;
use App\Models\WorkHours;
use App\Models\ServiceActivites;
use App\Models\Bidang;
use App\Models\Sops;

class Services extends Model
{
    protected $table = 'services';
    protected $guarded = [];

    /**
     * Model Event Hook: Otomatis mengisi foreign key bidang_id sebelum record dibuat
     */
    protected static function booted(): void
    {
        static::creating(function ($service) {
            if (empty($service->bidang_id)) {
                // 1. Ambil bidang_id dari user helpdesk bidang yang sedang login
                $userBidangId = auth()->user()?->bidang_id;

                // 2. Jika user tidak punya bidang_id, ambil ID pertama dari tabel master bidang
                $defaultBidangId = (new static)->getConnection()->table('bidang')->value('id') ?? 1;

                $service->bidang_id = $userBidangId ?? $defaultBidangId;
            }
        });
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Tickets::class, 'service_id');
    }

    public function ticketTasks(): HasMany
    {
        return $this->hasMany(TicketTask::class, 'service_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class, 'category_id');
    }

    public function serviceCategories(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class, 'category_id');
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

    public function sops(): HasMany
    {
        return $this->hasMany(Sops::class, 'service_id');
    }
}
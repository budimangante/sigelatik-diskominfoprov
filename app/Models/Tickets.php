<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tickets extends Model
{
    use SoftDeletes;

    protected $table = 'tickets';

    protected $fillable = [
        'ticket_number',
        'public_token',
        'user_id',
        'employment_id',
        'service_id',
        'ticket_status_id',
        'subject',
        'description',
        'perangkat_daerah_id',
        'bidang_id',
        'solved_at',
        'cancelled_at',
        'cancel_reason',
    ];

    protected $casts = [
        'solved_at'    => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    /* =========================================================================
     * ACCESSOR JALUR AMAN (ANTI ERROR BLADE)
     * ========================================================================= */

    public function getCodeAttribute(): ?string
    {
        return $this->attributes['ticket_number'] ?? null;
    }

    public function getTitleAttribute(): ?string
    {
        return $this->attributes['subject'] ?? null;
    }

    public function getPriorityAttribute(): string
    {
        return $this->attributes['priority'] ?? 'Sedang';
    }

    /**
     * Khusus Super Admin: Mengembalikan ARRAY badge (color & label)
     */
    public function getSlaBadgeAttribute(): array
    {
        if (!$this->created_at) {
            return [
                'status' => 'unknown',
                'label'  => 'Standar SLA',
                'color'  => 'bg-slate-100 text-slate-600 border-slate-200',
            ];
        }

        $statusName = strtolower($this->status?->name ?? '');
        if (in_array($statusName, ['selesai', 'closed', 'resolved', 'ditutup', 'solved'])) {
            return [
                'status' => 'completed',
                'label'  => 'Selesai',
                'color'  => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            ];
        }

        $dueAt = $this->created_at->copy()->addDay();
        $now = now();

        if ($now->greaterThan($dueAt)) {
            $diffHours = $now->diffInHours($dueAt);
            return [
                'status' => 'breached',
                'label'  => 'SLA Terlewat (' . ($diffHours > 0 ? "{$diffHours}j lalu" : 'Baru saja') . ')',
                'color'  => 'bg-rose-50 text-rose-700 border-rose-200 animate-pulse',
            ];
        }

        $remainingHours = $now->diffInHours($dueAt);
        $remainingMinutes = $now->diffInMinutes($dueAt) % 60;

        if ($remainingHours < 2) {
            return [
                'status' => 'warning',
                'label'  => "Sisa {$remainingMinutes} menit",
                'color'  => 'bg-amber-50 text-amber-700 border-amber-200',
            ];
        }

        return [
            'status' => 'safe',
            'label'  => "Sisa {$remainingHours}j {$remainingMinutes}m",
            'color'  => 'bg-blue-50 text-blue-700 border-blue-200',
        ];
    }

    /**
     * Khusus Triage Bidang: SELALU mengembalikan STRING (Mencegah htmlspecialchars crash)
     */
    public function getSlaInfoAttribute($value)
    {
        // 1. Jika controller Bidang sudah mengisi string (misal: "50 mnt tersisa" atau "Luar Jam Kerja")
        if (is_string($value) && trim($value) !== '') {
            return $value;
        }

        // 2. Jika dipanggil di view yang mengharapkan array badge (seperti Super Admin)
        if (is_null($value)) {
            return $this->sla_badge;
        }

        return (string) $value;
    }

    /* =========================================================================
     * RELASI ELOQUENT
     * ========================================================================= */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function employment(): BelongsTo
    {
        return $this->belongsTo(Employments::class, 'employment_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(TicketStatuses::class, 'ticket_status_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Services::class, 'service_id');
    }

    public function perangkatDaerah(): BelongsTo
    {
        return $this->belongsTo(PerangkatDaerah::class, 'perangkat_daerah_id');
    }

    public function bidang(): BelongsTo
    {
        return $this->belongsTo(Bidang::class, 'bidang_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(TicketTask::class, 'ticket_id');
    }

    public function ticketTask(): HasMany
    {
        return $this->tasks();
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(TicketStatusHistory::class, 'ticket_id')->orderBy('id', 'asc');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(TicketMessage::class, 'ticket_id');
    }

    public function ticketFiles(): BelongsToMany
    {
        return $this->belongsToMany(Files::class, 'ticket_files', 'ticket_id', 'file_id');
    }

    public function getRouteKeyName(): string
    {
        return 'ticket_number';
    }

    public function resolveRouteBinding($value, $field = null)
    {
        if ($field) {
            return $this->where($field, $value)->firstOrFail();
        }

        return $this->where(function ($query) use ($value) {
            $query->where('ticket_number', $value)
                ->orWhere('id', $value);
        })->firstOrFail();
    }
}
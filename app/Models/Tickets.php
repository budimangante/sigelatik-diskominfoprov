<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\Services; // Atau Service::class jika nama modelnya tunggal
use App\Models\TicketStatus;
use App\Models\TicketStatusHistory;
use App\Models\TicketMessage;
use App\Models\Employments;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tickets extends Model
{
    use SoftDeletes;

    // Pastikan tabel mengarah ke 'tickets'
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
        'solved_at',
        'cancelled_at',
        'cancel_reason',
    ];
    protected $guarded = [];

    // Relasi ke User Pelapor
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function employment(): BelongsTo
    {
        return $this->belongsTo(Employments::class, 'employment_id');
    }

    // Relasi ke Status Tiket
    public function status(): BelongsTo
    {
        return $this->belongsTo(TicketStatuses::class, 'ticket_status_id');
    }

    // Relasi ke Layanan yang diadukan
    public function service(): BelongsTo
    {
        return $this->belongsTo(Services::class, 'service_id');
    }

    // Relasi ke Riwayat Perubahan Status (Timeline pelacakan)
    public function statusHistories(): HasMany
    {
        // Gunakan orderBy('id', 'asc') agar runtut secara kronologis sesuai timeline
        return $this->hasMany(TicketStatusHistory::class, 'ticket_id')->orderBy('id', 'asc');
    }

    // Relasi ke Riwayat Pesan / Diskusi Tiket
    public function messages(): HasMany
    {
        return $this->hasMany(TicketMessage::class, 'ticket_id');
    }

    public function ticketFiles(): BelongsToMany
    {
        return $this->belongsToMany(
            Files::class,
            'ticket_files',
            'ticket_id',
            'file_id'
        );
    }

    public function ticketTask(): HasMany
    {
        return $this->hasMany(TicketTask::class, 'ticket_id');
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
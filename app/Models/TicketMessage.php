<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\User;
use App\Models\Tickets;
use App\Models\Files;

class TicketMessage extends Model
{
    protected $guarded = [];

    // Tambahkan cast datetime ini agar read_at otomatis jadi objek Carbon
    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Tickets::class, 'ticket_id');
    }

    public function ticketTask(): BelongsTo
    {
        return $this->belongsTo(TicketTask::class, 'ticket_task_id');
    }

    public function ticketMessagesFile(): BelongsToMany
    {
        return $this->belongsToMany(
            Files::class,
            'ticket_message_files',
            'ticket_message_id',
            'file_id'
        );
    }
}
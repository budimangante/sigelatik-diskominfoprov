<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Tickets;
use App\Models\TicketMessage;
use App\Models\Services;
use App\Models\TicketStatus;
use App\Models\User;

class TicketTask extends Model
{
    protected $table = 'tickets_task';

    protected $fillable = [
        'ticket_id',
        'service_id',
        'ticket_status_id',
        'created_by',
        'received_at',
        'started_at',
        'closed_at',
        'close_type',
        'updated_type',
    ];

    public function ticketTaskActivity():HasMany
    {
        return $this->hasMany(TicketTaskActivity::class, 'ticket_task_id');
    }

    public function ticketTaskAssignees(): HasMany
    {
        return $this->hasMany(TicketTaskAssignees::class, 'ticket_task_id');
    }

    public function ticketMessages(): HasMany
    {
        return $this->hasMany(TicketMessage::class);
    }
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Tickets::class, 'ticket_id');
    }

    public function ticketStatusHistori(): BelongsTo
    {
        return $this->belongsTo(TicketStatusHistory::class, 'ticket_status_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Services::class, 'service_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function taskReport(): HasMany
    {
        return $this->hasMany(TaskReports::class, 'ticket_task_id');
    }
}
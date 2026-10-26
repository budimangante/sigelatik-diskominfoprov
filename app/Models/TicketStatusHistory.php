<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketStatusHistory extends Model
{
    public $timestamps = false;
    protected $guarded = [];
    public function tickets():BelongsTo
    {
        return $this->belongsTo(Tickets::class, 'ticket_id');
    }
    public function pendingReason():BelongsTo
    {
        return $this->belongsTo(PendingReason::class, 'pending_reason_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    public function ticketStatuses():BelongsTo
    {
        return $this->belongsTo(TicketStatuses::class, 'ticket_status_id');
    }
}

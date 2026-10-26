<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketStatuses extends Model
{
    public function ticketStatusHistoy():BelongsTo
    {
        return $this->belongsTo(TicketStatusHistory::class, 'ticket_status_id');
    }
}

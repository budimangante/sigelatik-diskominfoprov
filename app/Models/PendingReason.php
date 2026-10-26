<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PendingReason extends Model
{
    public function taskStatusHistories():BelongsTo
    {
        return $this->belongsTo(TicketStatusHistory::class, 'pending_reason_id');
    }
}

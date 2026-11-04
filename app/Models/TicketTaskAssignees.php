<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketTaskAssignees extends Model
{
    protected $table = 'ticket_task_assignees';
    protected $guarded = [];
    public function employment():BelongsTo
    {
        return $this->belongsTo(Employments::class, 'employment_id');
    }
        
    public function ticketTask(): BelongsTo
    {
        return $this->belongsTo(TicketTask::class, 'ticket_task_id');
    }
}

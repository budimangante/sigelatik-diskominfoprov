<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketTaskActivity extends Model
{
    protected $table = 'ticket_task_activities';

    protected $fillable = [
        'ticket_task_id',
        'service_activities_id',
        'name_snapshot',
        'sort_order',
        'target_minutes_snapshot',
        'paused_seconds',
        'is_sla_met',
        'start_at',
        'completed_at',
        'due_at',
    ];

    protected $casts = [
        'is_sla_met'              => 'boolean',
        'target_minutes_snapshot' => 'integer',
        'paused_seconds'          => 'integer',
        'sort_order'              => 'integer',
        'start_at'                => 'datetime',
        'completed_at'            => 'datetime',
        'due_at'                  => 'datetime',
    ];

    // Relasi balik ke Task
    public function task(): BelongsTo
    {
        return $this->belongsTo(TicketTask::class, 'ticket_task_id');
    }

    // Relasi ke Master SOP jika masih ada
    public function masterActivity(): BelongsTo
    {
        return $this->belongsTo(ServiceActivites::class, 'service_activities_id');
    }
}
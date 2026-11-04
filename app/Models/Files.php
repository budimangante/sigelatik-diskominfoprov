<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Files extends Model
{
    protected $table = 'files';
    

    const UPDATED_AT = null;
    protected $fillable = [
        'disk',
        'path',
        'original_name',
        'mime_type',
        'size_bytes',
        'checksum_sha256',
        'uploaded_by',
    ];
    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function ticketMessagesFile(): BelongsToMany
        {
            return $this->belongsToMany(
                TicketMessage::class,
                'ticket_message_files',
                'file_id',
                'ticket_message_id'
            );
        }
    public function ticketFiles(): BelongsToMany
    {
        return $this->belongsToMany(
            Tickets::class,
            'ticket_files',
            'file_id',
            'ticket_id'
        );
    }

    
    public function sops(): HasMany
    {
        return $this->hasMany(Sops::class, 'file_id');
    }
        
    public function taskReportFiles(): BelongsToMany
    {
        return $this->belongsToMany(
            TaskReports::class,
            'task_report_files',
            'file_id',
            'task_report_id'
        );
    }
        // public function sop(): HasOne
        // {
            //     return $this->hasOne(Sops::class, 'file_id');
            // }


}
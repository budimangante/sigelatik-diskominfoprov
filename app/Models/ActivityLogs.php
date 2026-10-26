<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use App\Models\User;

class ActivityLogs extends Model
{
    protected $table = 'activity_logs';

    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'event',
        'subject_type',
        'subject_id',
        'properties',
        'ip_address',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relasi Polymorphic
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }
}
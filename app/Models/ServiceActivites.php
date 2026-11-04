<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceActivites extends Model
{
    protected $table = 'service_activities';
    protected $guarded = [];
    public function service(): BelongsTo
    {
        return $this->belongsTo(Services::class, 'service_id');
    }
}

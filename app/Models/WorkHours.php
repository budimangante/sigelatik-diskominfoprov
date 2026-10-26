<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkHours extends Model
{
    public function workhours(): BelongsTo
    {
        return $this->belongsTo(Services::class, 'service_id');
    }
}

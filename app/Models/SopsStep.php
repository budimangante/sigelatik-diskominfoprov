<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use SoapClient;

class SopsStep extends Model
{
    public function sop():BelongsTo
    {
        return $this->belongsTo(Sops::class, 'sop_id');
    }
}

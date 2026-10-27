<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SopsStep extends Model
{
    protected $table = 'sops_steps';
    
    public $timestamps = false;
    protected $guarded = ['id'];

    public function sop(): BelongsTo
    {
        return $this->belongsTo(Sops::class, 'sop_id');
    }
    public function steps(): HasMany
    {
        return $this->hasMany(SopsStep::class, 'sop_id')->orderBy('step_no', 'asc');
    }
}
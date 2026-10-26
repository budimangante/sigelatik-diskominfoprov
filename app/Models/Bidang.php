<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Employments;
use App\Models\PerangkatDaerah;

class Bidang extends Model
{
    protected $table = 'bidang';

    protected $fillable = [
        'perangkat_daerah_id',
        'name',
    ];

    
    public function employments(): HasMany
    {
        return $this->hasMany(Employments::class, 'bidang_id');
    }

    public function perangkatDaerah(): BelongsTo
    {
        return $this->belongsTo(PerangkatDaerah::class, 'perangkat_daerah_id');
    }

    public function service(): HasMany
    {
        return $this->hasMany(Services::class, 'bidang_id');
    }
}
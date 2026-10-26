<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
// use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class PerangkatDaerah extends Model
{
    protected $table = 'perangkat_daerah';
    protected $fillable = [
        'name',
        'code',
        'type',
    ];
    //
    public function employments(): HasMany
    {
        return $this->hasMany(Employments::class, 'perangkat_daerah_id');
    }

    public function bidang(): HasMany
    {
        // Parameter 2: foreign key di tabel 'bidang'
        return $this->hasMany(Bidang::class, 'perangkat_daerah_id');
    }
    public function serviceCategories(): HasMany
    {
        return $this->hasMany(ServiceCategory::class, 'perangkat_daerah_id');
    }
    
    public function holidays():HasMany
    {
        return $this->hasMany(Holidays::class, 'perangkat_daerah_id');
    }

    //ketika harus langsung menarik semua Layanan (Services) milik suatu Perangkat Daerah
    //     public function services(): HasManyThrough
    // {
    //     return $this->hasManyThrough(
    //         Services::class,
    //         ServiceCategory::class,
    //         'perangkat_daerah_id', // Foreign key di tabel perantara (service_categories)
    //         'service_category_id', // Foreign key di tabel tujuan (services)
    //     );
    // }
}

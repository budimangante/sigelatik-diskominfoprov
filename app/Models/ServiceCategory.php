<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Services;

class ServiceCategory extends Model
{
    protected $table = 'service_categories';
    protected $guarded = [];

    protected static function booted(): void
    {
        static::creating(function ($category) {
            if (empty($category->perangkat_daerah_id)) {
                // 1. Coba ambil dari user yang login jika ada kolom perangkat_daerah_id
                $userPdId = auth()->user()?->perangkat_daerah_id;

                // 2. Fallback otomatis ke ID pertama dari tabel perangkat_daerah
                $defaultPdId = (new static)->getConnection()->table('perangkat_daerah')->value('id') ?? 1;

                $category->perangkat_daerah_id = $userPdId ?? $defaultPdId;
            }
            if (empty($category->description)) {
                $category->description = 'Deskripsi default untuk kategori layanan.';
            }
        });
    }

    public function services(): HasMany
    {
        return $this->hasMany(Services::class, 'category_id');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketStatuses extends Model
{
    protected $table = 'ticket_statuses';
    protected $guarded = [];
    protected $fillable = [
        'name',
        'code',
        'color',
    ];
    public static function ensureAllStatusesExist(): void
    {
        $statuses = [
            1 => ['name' => 'Baru', 'code' => 'baru', 'sort_order' => 1],
            2 => ['name' => 'Diproses', 'code' => 'diproses', 'sort_order' => 2],
            3 => ['name' => 'Dijeda', 'code' => 'dijeda', 'sort_order' => 3],
            4 => ['name' => 'Selesai', 'code' => 'selesai', 'sort_order' => 4],
            5 => ['name' => 'Ditolak', 'code' => 'ditolak', 'sort_order' => 5],
        ];

        foreach ($statuses as $id => $data) {
            static::firstOrCreate(['id' => $id], $data);
        }
    }
    public function ticketStatusHistory():BelongsTo
    {
        return $this->belongsTo(TicketStatusHistory::class, 'ticket_status_id');
    }
}

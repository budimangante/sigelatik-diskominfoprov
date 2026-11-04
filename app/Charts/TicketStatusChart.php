<?php

namespace App\Charts;

use App\Models\Tickets;
use Illuminate\Support\Facades\DB;

class TicketStatusChart
{
    public static function build(): array
    {
        // Status: 1=NEW, 2=PROCESS, 3=PENDING, 4=SOLVED, 5=CANCELLED
        $statusCounts = Tickets::select('ticket_status_id', DB::raw('count(*) as total'))
            ->groupBy('ticket_status_id')
            ->pluck('total', 'ticket_status_id')
            ->toArray();

        return [
            'labels'   => ['NEW', 'PROCESS', 'PENDING', 'SOLVED', 'CANCELLED'],
            'datasets' => [
                [
                    'label'           => 'Jumlah Tiket',
                    'data'            => [
                        $statusCounts[1] ?? 0,
                        $statusCounts[2] ?? 0,
                        $statusCounts[3] ?? 0,
                        $statusCounts[4] ?? 0,
                        $statusCounts[5] ?? 0,
                    ],
                    'backgroundColor' => [
                        '#0ea5e9', // NEW (Cyan/Light Blue)
                        '#2563eb', // PROCESS (Blue)
                        '#f59e0b', // PENDING (Orange)
                        '#10b981', // SOLVED (Green)
                        '#64748b', // CANCELLED (Slate)
                    ],
                    'borderRadius'    => 4,
                    'barThickness'    => 14,
                ]
            ]
        ];
    }
}
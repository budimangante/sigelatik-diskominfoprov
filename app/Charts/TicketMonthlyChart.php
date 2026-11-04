<?php

namespace App\Charts;

use App\Models\Tickets;
use Illuminate\Support\Facades\DB;

class TicketMonthlyChart
{
    public static function build(int $months = 6): array
    {
        $data = Tickets::select(
            DB::raw('DATE_FORMAT(created_at, "%b %Y") as month_label'),
            DB::raw('COUNT(*) as total')
        )
        ->where('created_at', '>=', now()->subMonths($months))
        ->groupBy('month_label')
        ->orderBy(DB::raw('MIN(created_at)'), 'asc')
        ->get();

        return [
            'labels'   => $data->pluck('month_label')->toArray(),
            'datasets' => [
                [
                    'label'                => 'Tiket Masuk',
                    'data'                 => $data->pluck('total')->toArray(),
                    'borderColor'          => '#2563eb',
                    'backgroundColor'      => 'rgba(37, 99, 235, 0.1)',
                    'fill'                 => true,
                    'tension'              => 0.35,
                    'borderWidth'          => 2,
                    'pointRadius'          => 3,
                    'pointBackgroundColor' => '#2563eb',
                ]
            ]
        ];
    }
}
<?php

namespace App\Charts;

use App\Models\Tickets;
use App\Models\TicketStatuses;

class MonitoringLayananCharts
{
    public static function build(): array
    {
        $tickets = Tickets::with(['service.category', 'status'])->get();
        $totalTickets = $tickets->count();

        $resolvedStatusIds = TicketStatuses::where(function ($q) {
            $q->where('code', 'like', '%selesai%')
              ->orWhere('code', 'like', '%closed%')
              ->orWhere('code', 'like', '%solved%')
              ->orWhere('name', 'like', '%selesai%')
              ->orWhere('name', 'like', '%tutup%');
        })->pluck('id')->toArray();

        $groupedServices = $tickets->groupBy(function ($t) {
            return $t->service?->name ?? 'Layanan Umum / Lainnya';
        });

        $serviceLabels      = [];
        $totalPerService    = [];
        $resolvedPerService = [];
        $processPerService  = [];
        $reportingTable     = [];

        $colors = [
            '#1a3668', '#2563eb', '#0284c7', '#0d9488', 
            '#10b981', '#f59e0b', '#f43f5e', '#8b5cf6', '#64748b'
        ];
        $colorIdx = 0;

        foreach ($groupedServices as $serviceName => $items) {
            $tot = $items->count();
            $res = $items->filter(fn($t) => in_array($t->ticket_status_id, $resolvedStatusIds) || !empty($t->solved_at))->count();
            $proc = max(0, $tot - $res);
            $solveRate = $tot > 0 ? round(($res / $tot) * 100, 1) : 0;
            $color = $colors[$colorIdx % count($colors)];

            $serviceLabels[]      = $serviceName;
            $totalPerService[]    = $tot;
            $resolvedPerService[] = $res;
            $processPerService[]  = $proc;

            $reportingTable[] = [
                'name'        => $serviceName,
                'category'    => $items->first()->service?->category?->name ?? 'Umum',
                'total'       => $tot,
                'process'     => $proc,
                'resolved'    => $res,
                'solve_rate'  => $solveRate,
                'sla_target'  => ($items->first()->service?->sla_process_minutes ?? 240) / 60,
                'color'       => $color,
            ];

            $colorIdx++;
        }

        $reportingSorted = collect($reportingTable)->sortByDesc('total')->values()->toArray();

        return [
            'total_services'       => count($serviceLabels),
            'total_tickets'        => $totalTickets,
            'top_service'          => !empty($reportingSorted) ? $reportingSorted[0]['name'] : 'Belum Ada Tiket',
            'global_solve_rate'    => $totalTickets > 0 ? round((array_sum($resolvedPerService) / $totalTickets) * 100, 1) : 0,
            'serviceBarChart'      => [
                'labels'   => $serviceLabels,
                'datasets' => [
                    [
                        'label'           => 'Total Masuk',
                        'data'            => $totalPerService,
                        'backgroundColor' => '#e2e8f0',
                        'borderRadius'    => 6,
                        'barThickness'    => 18,
                    ],
                    [
                        'label'           => 'Diproses',
                        'data'            => $processPerService,
                        'backgroundColor' => '#3b82f6',
                        'borderRadius'    => 6,
                        'barThickness'    => 18,
                    ],
                    [
                        'label'           => 'Selesai',
                        'data'            => $resolvedPerService,
                        'backgroundColor' => '#10b981',
                        'borderRadius'    => 6,
                        'barThickness'    => 18,
                    ]
                ]
            ],
            'serviceDoughnutChart' => [
                'labels'   => $serviceLabels,
                'datasets' => [
                    [
                        'data'            => $totalPerService,
                        'backgroundColor' => array_slice($colors, 0, count($serviceLabels)),
                        'borderWidth'     => 3,
                        'borderColor'     => '#ffffff',
                    ]
                ]
            ],
            'reportingTable'       => $reportingSorted,
        ];
    }
}
<?php

namespace App\Charts;

use App\Models\Tickets;
use App\Models\TicketStatuses;

class PrioritasTicketChart
{
    public static function build(): array
    {
        $tickets = Tickets::with('status')->get();

        $counts = [
            'critical' => $tickets->filter(fn($t) => in_array(strtolower($t->priority ?? ''), ['critical', 'kritis']))->count(),
            'high'     => $tickets->filter(fn($t) => in_array(strtolower($t->priority ?? ''), ['high', 'tinggi']))->count(),
            'medium'   => $tickets->filter(fn($t) => in_array(strtolower($t->priority ?? ''), ['medium', 'sedang']) || empty($t->priority))->count(),
            'low'      => $tickets->filter(fn($t) => in_array(strtolower($t->priority ?? ''), ['low', 'rendah']))->count(),
        ];

        $total = array_sum($counts);

        $resolvedStatusIds = TicketStatuses::where(function ($q) {
            $q->where('code', 'like', '%selesai%')
              ->orWhere('code', 'like', '%closed%')
              ->orWhere('code', 'like', '%solved%')
              ->orWhere('name', 'like', '%selesai%')
              ->orWhere('name', 'like', '%tutup%');
        })->pluck('id')->toArray();

        $resolvedCounts = [
            'critical' => $tickets->filter(fn($t) => in_array(strtolower($t->priority ?? ''), ['critical', 'kritis']) && (in_array($t->ticket_status_id, $resolvedStatusIds) || !empty($t->solved_at)))->count(),
            'high'     => $tickets->filter(fn($t) => in_array(strtolower($t->priority ?? ''), ['high', 'tinggi']) && (in_array($t->ticket_status_id, $resolvedStatusIds) || !empty($t->solved_at)))->count(),
            'medium'   => $tickets->filter(fn($t) => (in_array(strtolower($t->priority ?? ''), ['medium', 'sedang']) || empty($t->priority)) && (in_array($t->ticket_status_id, $resolvedStatusIds) || !empty($t->solved_at)))->count(),
            'low'      => $tickets->filter(fn($t) => in_array(strtolower($t->priority ?? ''), ['low', 'rendah']) && (in_array($t->ticket_status_id, $resolvedStatusIds) || !empty($t->solved_at)))->count(),
        ];

        $doughnutChart = [
            'labels'   => ['Kritis', 'Tinggi', 'Sedang', 'Rendah'],
            'datasets' => [
                [
                    'data'            => array_values($counts),
                    'backgroundColor' => ['#e11d48', '#f59e0b', '#1a3668', '#94a3b8'],
                    'borderWidth'     => 3,
                    'borderColor'     => '#ffffff',
                    'hoverOffset'     => 6,
                ]
            ]
        ];

        $comparisonBarChart = [
            'labels'   => ['Kritis', 'Tinggi', 'Sedang', 'Rendah'],
            'datasets' => [
                [
                    'label'           => 'Total Masuk',
                    'data'            => array_values($counts),
                    'backgroundColor' => '#e2e8f0',
                    'borderRadius'    => 8,
                    'barThickness'    => 24,
                ],
                [
                    'label'           => 'Telah Selesai',
                    'data'            => array_values($resolvedCounts),
                    'backgroundColor' => '#10b981',
                    'borderRadius'    => 8,
                    'barThickness'    => 24,
                ]
            ]
        ];

        $slaMatrixChart = [
            'labels'   => ['Kritis (4 Jam)', 'Tinggi (8 Jam)', 'Sedang (24 Jam)', 'Rendah (48 Jam)'],
            'datasets' => [
                [
                    'label'           => 'Target Maksimal (Jam)',
                    'data'            => [4, 8, 24, 48],
                    'backgroundColor' => ['#f43f5e', '#fbbf24', '#3b82f6', '#94a3b8'],
                    'borderRadius'    => 8,
                    'barThickness'    => 20,
                ]
            ]
        ];

        return [
            'counts'             => $counts,
            'total'              => $total,
            'resolved'           => $resolvedCounts,
            'doughnutChart'      => $doughnutChart,
            'comparisonBarChart' => $comparisonBarChart,
            'slaMatrixChart'     => $slaMatrixChart,
        ];
    }
}
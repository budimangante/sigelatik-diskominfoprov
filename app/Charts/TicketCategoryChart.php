<?php

namespace App\Charts;

use App\Models\ServiceCategory;
use Illuminate\Support\Facades\DB;
use App\Models\Tickets;
use App\Models\TicketStatuses;

class TicketCategoryChart
{
    public static function build(): array
    {
        $categories = ServiceCategory::withCount('services')
            ->leftJoin('services', 'service_categories.id', '=', 'services.category_id')
            ->leftJoin('tickets', 'services.id', '=', 'tickets.service_id')
            ->select('service_categories.name', DB::raw('COUNT(tickets.id) as total_tickets'))
            ->groupBy('service_categories.id', 'service_categories.name')
            ->orderByDesc('total_tickets')
            ->take(6)
            ->get();

        return [
            'labels'   => $categories->pluck('name')->toArray(),
            'datasets' => [
                [
                    'label'           => 'Jumlah Tiket',
                    'data'            => $categories->pluck('total_tickets')->toArray(),
                    'backgroundColor' => '#3b82f6',
                    'borderRadius'    => 6,
                    'maxBarThickness' => 28,
                ]
            ]
        ];
    }

    public static function subbuild(): array
    {
        // 1. Ambil seluruh tiket dengan relasi service, category, dan status
        $tickets = Tickets::with(['service.category', 'status'])->get();
        $totalTickets = $tickets->count();

        // 2. Ambil ID status selesai/closed secara dinamis
        $resolvedStatusIds = TicketStatuses::where(function ($q) {
            $q->where('code', 'like', '%selesai%')
              ->orWhere('code', 'like', '%closed%')
              ->orWhere('code', 'like', '%solved%')
              ->orWhere('name', 'like', '%selesai%')
              ->orWhere('name', 'like', '%tutup%');
        })->pluck('id')->toArray();

        // 3. Kelompokkan tiket berdasarkan Kategori Layanan
        $grouped = $tickets->groupBy(function ($t) {
            return $t->service?->category?->name 
                ?? $t->service?->name 
                ?? 'Lainnya / Umum';
        });

        // 4. Susun metrik dan data visual
        $categoryNames  = [];
        $categoryCounts = [];
        $resolvedCounts = [];
        $processCounts  = [];
        $tableSummary   = [];

        $colors = [
            '#1a3668', '#2563eb', '#0284c7', '#0d9488', 
            '#10b981', '#f59e0b', '#f43f5e', '#8b5cf6', '#64748b'
        ];

        $colorIndex = 0;
        foreach ($grouped as $name => $items) {
            $totalCount    = $items->count();
            $resolvedCount = $items->filter(fn($t) => in_array($t->ticket_status_id, $resolvedStatusIds) || !empty($t->solved_at))->count();
            $processCount  = max(0, $totalCount - $resolvedCount);
            $solveRate     = $totalCount > 0 ? round(($resolvedCount / $totalCount) * 100, 1) : 0;
            $color         = $colors[$colorIndex % count($colors)];

            $categoryNames[]  = $name;
            $categoryCounts[] = $totalCount;
            $resolvedCounts[] = $resolvedCount;
            $processCounts[]  = $processCount;

            $tableSummary[] = [
                'name'        => $name,
                'total'       => $totalCount,
                'resolved'    => $resolvedCount,
                'process'     => $processCount,
                'solve_rate'  => $solveRate,
                'color'       => $color,
            ];

            $colorIndex++;
        }

        // Kategori terbanyak dan persentase penyelesaian global
        $topCategory = !empty($tableSummary) 
            ? collect($tableSummary)->sortByDesc('total')->first()['name'] 
            : 'Belum ada data';

        $totalResolved = !empty($tableSummary) ? array_sum($resolvedCounts) : 0;
        $globalSolveRate = $totalTickets > 0 ? round(($totalResolved / $totalTickets) * 100, 1) : 0;

        // 5. Konfigurasi Doughnut Chart (Distribusi Porsi Kategori)
        $doughnutChart = [
            'labels'   => $categoryNames,
            'datasets' => [
                [
                    'data'            => $categoryCounts,
                    'backgroundColor' => array_slice($colors, 0, count($categoryNames)),
                    'borderWidth'     => 3,
                    'borderColor'     => '#ffffff',
                    'hoverOffset'     => 6,
                ]
            ]
        ];

        // 6. Konfigurasi Bar Chart (Masuk vs Selesai per Kategori)
        $comparisonBarChart = [
            'labels'   => $categoryNames,
            'datasets' => [
                [
                    'label'           => 'Total Masuk',
                    'data'            => $categoryCounts,
                    'backgroundColor' => '#e2e8f0',
                    'borderRadius'    => 8,
                    'barThickness'    => 20,
                ],
                [
                    'label'           => 'Telah Selesai',
                    'data'            => $resolvedCounts,
                    'backgroundColor' => '#10b981',
                    'borderRadius'    => 8,
                    'barThickness'    => 20,
                ],
                [
                    'label'           => 'Sedang Ditangani',
                    'data'            => $processCounts,
                    'backgroundColor' => '#3b82f6',
                    'borderRadius'    => 8,
                    'barThickness'    => 20,
                ]
            ]
        ];

        return [
            'total_categories'   => count($categoryNames),
            'total_tickets'      => $totalTickets,
            'top_category'       => $topCategory,
            'global_solve_rate'  => $globalSolveRate,
            'doughnutChart'      => $doughnutChart,
            'comparisonBarChart' => $comparisonBarChart,
            'tableSummary'       => $tableSummary,
        ];
    }
}
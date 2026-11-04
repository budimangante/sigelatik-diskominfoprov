<?php

namespace App\Charts;

use App\Models\PerangkatDaerah;
use App\Models\Tickets;
use App\Models\TicketStatuses;

class SeranganKeamananChart
{
    public static function build(): array
    {
        // 1. FILTER KETAT: Hanya ambil tiket yang BENAR-BENAR terkait Layanan Keamanan / CSIRT
        $securityTickets = Tickets::with(['perangkatDaerah', 'service.category', 'status'])
            ->where(function ($q) {
                // Berdasarkan Kategori Layanan / Bidang Keamanan
                $q->whereHas('service.category', function ($cat) {
                    $cat->where('name', 'like', '%keamanan%')
                        ->orWhere('name', 'like', '%siber%')
                        ->orWhere('name', 'like', '%csirt%')
                        ->orWhere('name', 'like', '%persandian%');
                })
                // Atau nama layanan teknis spesifik insiden siber
                ->orWhereHas('service', function ($svc) {
                    $svc->where('name', 'like', '%keamanan%')
                        ->orWhere('name', 'like', '%siber%')
                        ->orWhere('name', 'like', '%csirt%')
                        ->orWhere('name', 'like', '%deface%')
                        ->orWhere('name', 'like', '%malware%')
                        ->orWhere('name', 'like', '%insiden%');
                });
            })
            ->get();

        $totalIncidents = $securityTickets->count();

        // 2. Klasifikasi Jenis Serangan (Hanya dari tiket keamanan yang valid)
        $attackTypes = [
            'Web Defacement / Judi Online'          => 0,
            'Distributed Denial of Service (DDoS)' => 0,
            'Phishing & Rekayasa Sosial'           => 0,
            'Malware / Ransomware'                 => 0,
            'SQL Injection & Kebocoran Data'       => 0,
            'Brute Force / Percobaan Login Ilegal' => 0,
        ];

        foreach ($securityTickets as $t) {
            $text = strtolower(($t->subject ?? '') . ' ' . ($t->description ?? '') . ' ' . ($t->service?->name ?? ''));

            if (str_contains($text, 'deface') || str_contains($text, 'slot') || str_contains($text, 'judi')) {
                $attackTypes['Web Defacement / Judi Online']++;
            } elseif (str_contains($text, 'ddos') || str_contains($text, 'syn flood') || str_contains($text, 'traffic flood')) {
                $attackTypes['Distributed Denial of Service (DDoS)']++;
            } elseif (str_contains($text, 'phishing') || str_contains($text, 'email palsu') || str_contains($text, 'social engineering')) {
                $attackTypes['Phishing & Rekayasa Sosial']++;
            } elseif (str_contains($text, 'ransom') || str_contains($text, 'malware') || str_contains($text, 'trojan') || str_contains($text, 'terenkripsi')) {
                $attackTypes['Malware / Ransomware']++;
            } elseif (str_contains($text, 'sql') || str_contains($text, 'injection') || str_contains($text, 'leak') || str_contains($text, 'kebocoran')) {
                $attackTypes['SQL Injection & Kebocoran Data']++;
            } else {
                $attackTypes['Brute Force / Percobaan Login Ilegal']++;
            }
        }

        // 3. Top 10 OPD Paling Rentan (Hanya hitung jika OPD tersebut punya tiket keamanan)
        $top10List = [];
        if ($totalIncidents > 0) {
            $opdGrouped = $securityTickets->groupBy('perangkat_daerah_id');

            foreach ($opdGrouped as $opdId => $items) {
                $opdName = $items->first()->perangkatDaerah?->name 
                    ?? PerangkatDaerah::find($opdId)?->name 
                    ?? "OPD #{$opdId}";

                $top10List[] = [
                    'id'       => $opdId,
                    'name'     => $opdName,
                    'total'    => $items->count(),
                    'critical' => $items->filter(fn($x) => in_array(strtolower($x->priority ?? ''), ['critical', 'kritis']))->count(),
                    'resolved' => $items->filter(fn($x) => !empty($x->solved_at) || in_array($x->ticket_status_id, [4, 5]))->count(),
                ];
            }
        }

        $top10Sorted = collect($top10List)->sortByDesc('total')->take(10)->values()->toArray();

        $topOpdNames  = !empty($top10Sorted) ? array_column($top10Sorted, 'name') : ['Belum ada insiden'];
        $topOpdCounts = !empty($top10Sorted) ? array_column($top10Sorted, 'total') : [0];

        // 4. Tren Serangan 6 Bulan Terakhir
        $monthlyLabels = [];
        $monthlyCounts = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthlyLabels[] = $month->translatedFormat('M Y');
            $monthlyCounts[] = $securityTickets->filter(function ($t) use ($month) {
                return $t->created_at && \Carbon\Carbon::parse($t->created_at)->format('Y-m') === $month->format('Y-m');
            })->count();
        }

        // 5. Dataset Chart.js: Horizontal Bar (Top 10 OPD)
        $topOpdChart = [
            'labels'   => $topOpdNames,
            'datasets' => [
                [
                    'label'           => 'Jumlah Insiden Keamanan',
                    'data'            => $topOpdCounts,
                    'backgroundColor' => $totalIncidents > 0 ? '#e11d48' : '#cbd5e1',
                    'borderRadius'    => 6,
                    'barThickness'    => 16,
                ]
            ]
        ];

        // 6. Dataset Chart.js: Doughnut (Jenis Serangan)
        $attackValues = array_values($attackTypes);
        $hasAttacks = array_sum($attackValues) > 0;

        $attackTypeChart = [
            'labels'   => $hasAttacks ? array_keys($attackTypes) : ['Belum Ada Insiden Terdeteksi'],
            'datasets' => [
                [
                    'data'            => $hasAttacks ? $attackValues : [1],
                    'backgroundColor' => $hasAttacks 
                        ? ['#e11d48', '#f59e0b', '#2563eb', '#7c3aed', '#059669', '#ea580c'] 
                        : ['#e2e8f0'],
                    'borderWidth'     => 3,
                    'borderColor'     => '#ffffff',
                ]
            ]
        ];

        // 7. Dataset Chart.js: Line Chart (Tren Bulanan)
        $trendChart = [
            'labels'   => $monthlyLabels,
            'datasets' => [
                [
                    'label'                => 'Insiden Siber Bulanan',
                    'data'                 => $monthlyCounts,
                    'borderColor'          => '#e11d48',
                    'backgroundColor'      => 'rgba(225, 29, 72, 0.08)',
                    'fill'                 => true,
                    'tension'              => 0.35,
                    'borderWidth'          => 2.5,
                    'pointRadius'          => 4,
                    'pointBackgroundColor' => '#e11d48',
                ]
            ]
        ];

        return [
            'total_incidents' => $totalIncidents,
            'most_vulnerable' => $top10Sorted[0]['name'] ?? 'Nihil / Belum Ada Laporan',
            'dominant_attack' => $hasAttacks ? array_search(max($attackTypes), $attackTypes) : 'Nihil / Sistem Aman',
            'top10_opd'       => $top10Sorted,
            'attack_types'    => $attackTypes,
            'topOpdChart'     => $topOpdChart,
            'attackTypeChart' => $attackTypeChart,
            'trendChart'      => $trendChart,
        ];
    }
}
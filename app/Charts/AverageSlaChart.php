<?php

namespace App\Charts;

use App\Models\Tickets;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AverageSlaChart
{
    public static function build(int $months = 6): array
    {
        // 1. Buat kerangka 6 bulan terakhir secara urut
        $timeline = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $key = $date->format('Y-m');
            $timeline[$key] = [
                'label'    => $date->translatedFormat('M Y'),
                'resp_sum' => 0,
                'proc_sum' => 0,
                'count'    => 0,
            ];
        }

        // 2. Ambil data tiket 6 bulan terakhir beserta relasi layanannya
        $tickets = Tickets::with('service')
            ->where('created_at', '>=', now()->subMonths($months)->startOfMonth())
            ->get();

        $ticketIds = $tickets->pluck('id');

        // 3. Deteksi tabel task secara dinamis (tickets_task, ticket_task, atau ticket_tasks)
        $taskTable = null;
        foreach (['tickets_task', 'ticket_task', 'ticket_tasks'] as $tbl) {
            if (Schema::hasTable($tbl)) {
                $taskTable = $tbl;
                break;
            }
        }

        $tasks = collect();
        if ($taskTable && $ticketIds->isNotEmpty()) {
            $tasks = DB::table($taskTable)
                ->whereIn('ticket_id', $ticketIds)
                ->get()
                ->keyBy('ticket_id');
        }

        // 4. Kalkulasi durasi menit SLA per tiket di PHP (Bebas dari risiko column not found)
        foreach ($tickets as $ticket) {
            $createdAt = Carbon::parse($ticket->created_at);
            $monthKey = $createdAt->format('Y-m');

            if (!isset($timeline[$monthKey])) {
                continue;
            }

            $task = $tasks->get($ticket->id);

            // A. Durasi SLA 1 (Respon / Triage)
            $respMinutes = null;
            if ($task && !empty($task->received_at)) {
                $respMinutes = $createdAt->diffInMinutes(Carbon::parse($task->received_at));
            }
            if ($respMinutes === null || $respMinutes <= 0) {
                // Fallback aman ke properti service jika ada, default 60 menit
                $respMinutes = (int) ($ticket->service?->sla_response_minutes ?? 60);
            }
            $respMinutes = max(5, $respMinutes);

            // B. Durasi SLA 2 (Penanganan / Proses Teknisi)
            $procMinutes = null;
            if ($task && !empty($task->started_at)) {
                $endTime = ($ticket->ticket_status_id == 4) 
                    ? Carbon::parse($ticket->updated_at) 
                    : Carbon::parse($task->updated_at ?? $ticket->updated_at);

                $procMinutes = Carbon::parse($task->started_at)->diffInMinutes($endTime);
            }
            if ($procMinutes === null || $procMinutes <= 0) {
                // Fallback aman ke sla_process_minutes atau sla_minutes, default 240 menit (4 jam)
                $procMinutes = (int) ($ticket->service?->sla_process_minutes ?? $ticket->service?->sla_minutes ?? 240);
            }
            $procMinutes = max(10, $procMinutes);

            $timeline[$monthKey]['resp_sum'] += $respMinutes;
            $timeline[$monthKey]['proc_sum'] += $procMinutes;
            $timeline[$monthKey]['count']++;
        }

        // 5. Susun array dataset untuk Chart.js
        $labels = [];
        $responseSla = [];
        $processSla = [];

        foreach ($timeline as $item) {
            $labels[] = $item['label'];
            if ($item['count'] > 0) {
                $responseSla[] = (int) round($item['resp_sum'] / $item['count']);
                $processSla[]  = (int) round($item['proc_sum'] / $item['count']);
            } else {
                $responseSla[] = 0;
                $processSla[]  = 0;
            }
        }

        return [
            'labels'   => $labels,
            'datasets' => [
                [
                    'label'                => 'Rata-rata Respon (SLA 1)',
                    'data'                 => $responseSla,
                    'borderColor'          => '#2563eb', // Biru
                    'backgroundColor'      => 'rgba(37, 99, 235, 0.08)',
                    'fill'                 => true,
                    'tension'              => 0.35,
                    'borderWidth'          => 2.5,
                    'pointRadius'          => 3.5,
                    'pointBackgroundColor' => '#2563eb',
                ],
                [
                    'label'                => 'Rata-rata Penanganan (SLA 2)',
                    'data'                 => $processSla,
                    'borderColor'          => '#f59e0b', // Oranye / Amber
                    'backgroundColor'      => 'rgba(245, 158, 11, 0.08)',
                    'fill'                 => true,
                    'tension'              => 0.35,
                    'borderWidth'          => 2.5,
                    'pointRadius'          => 3.5,
                    'pointBackgroundColor' => '#f59e0b',
                ]
            ]
        ];
    }
}
<?php

namespace App\Charts;

use App\Models\PerangkatDaerah;
use App\Models\Tickets;
use App\Models\TicketStatuses;
use Carbon\Carbon;

class LaporanSlaDinasService
{
    public static function build(): array
    {
        $tickets = Tickets::with(['perangkatDaerah', 'service.category', 'status'])->get();
        $totalTickets = $tickets->count();

        // Ambil status tiket selesai
        $resolvedStatusIds = TicketStatuses::where(function ($q) {
            $q->where('code', 'like', '%selesai%')
              ->orWhere('code', 'like', '%closed%')
              ->orWhere('code', 'like', '%solved%')
              ->orWhere('name', 'like', '%selesai%')
              ->orWhere('name', 'like', '%tutup%');
        })->pluck('id')->toArray();

        $groupedDinas = $tickets->groupBy('perangkat_daerah_id');
        $laporanList = [];

        $allSlaDinasHours = [];
        $totalResolvedGlobal = 0;

        foreach ($groupedDinas as $opdId => $items) {
            $totalDinas = $items->count();
            $resolvedTickets = $items->filter(fn($t) => in_array($t->ticket_status_id, $resolvedStatusIds) || !empty($t->solved_at));
            $resolvedCount = $resolvedTickets->count();
            $processCount = max(0, $totalDinas - $resolvedCount);
            $totalResolvedGlobal += $resolvedCount;

            $dinasName = $items->first()->perangkatDaerah?->name 
                ?? PerangkatDaerah::find($opdId)?->name 
                ?? "Perangkat Daerah #{$opdId}";

            $avgTargetBidangHours = round($items->avg(function ($t) {
                return (int) ($t->service?->sla_process_minutes ?? $t->service?->sla_minutes ?? 240);
            }) / 60, 1);

            $sumTeknisiMinutes = 0;
            $ticketOnTimeCount = 0;

            if ($resolvedTickets->isNotEmpty()) {
                foreach ($resolvedTickets as $resTicket) {
                    $createdAt = Carbon::parse($resTicket->created_at);
                    $solvedAt = !empty($resTicket->solved_at) 
                        ? Carbon::parse($resTicket->solved_at) 
                        : Carbon::parse($resTicket->updated_at);

                    $durasiMenit = max(15, $createdAt->diffInMinutes($solvedAt));
                    $sumTeknisiMinutes += $durasiMenit;

                    $targetMenit = (int) ($resTicket->service?->sla_process_minutes ?? 240);
                    if ($durasiMenit <= $targetMenit) {
                        $ticketOnTimeCount++;
                    }
                }
                $avgRealisasiTeknisiHours = round(($sumTeknisiMinutes / $resolvedCount) / 60, 1);
                $kepatuhanPercent = round(($ticketOnTimeCount / $resolvedCount) * 100, 1);
            } else {
                $avgRealisasiTeknisiHours = $avgTargetBidangHours;
                $kepatuhanPercent = 100.0;
            }

            $slaDinasFinalHours = round(($avgTargetBidangHours + $avgRealisasiTeknisiHours) / 2, 1);
            $allSlaDinasHours[] = $slaDinasFinalHours;

            if ($kepatuhanPercent >= 90) {
                $statusKinerja = 'Sangat Baik';
                $badgeColor = 'emerald';
            } elseif ($kepatuhanPercent >= 75) {
                $statusKinerja = 'Baik';
                $badgeColor = 'blue';
            } else {
                $statusKinerja = 'Perlu Evaluasi';
                $badgeColor = 'amber';
            }

            $laporanList[] = [
                'id'                      => $opdId,
                'name'                    => $dinasName,
                'total_ticket'            => $totalDinas,
                'resolved_ticket'         => $resolvedCount,
                'process_ticket'          => $processCount,
                'avg_target_bidang'       => $avgTargetBidangHours,
                'avg_realisasi_teknisi'   => $avgRealisasiTeknisiHours,
                'avg_sla_dinas'           => $slaDinasFinalHours,
                'kepatuhan_percent'       => $kepatuhanPercent,
                'status_kinerja'          => $statusKinerja,
                'badge_color'             => $badgeColor,
            ];
        }

        $laporanSorted = collect($laporanList)->sortByDesc('total_ticket')->values()->toArray();

        $globalAvgSla = !empty($allSlaDinasHours) ? round(array_sum($allSlaDinasHours) / count($allSlaDinasHours), 1) : 2.5;
        $globalSolveRate = $totalTickets > 0 ? round(($totalResolvedGlobal / $totalTickets) * 100, 1) : 100.0;

        return [
            'total_dinas'       => count($laporanList),
            'total_tickets'     => $totalTickets,
            'global_avg_sla'    => str_replace('.', ',', (string) $globalAvgSla),
            'global_solve_rate' => $globalSolveRate,
            'laporan_table'     => $laporanSorted,
        ];
    }
}
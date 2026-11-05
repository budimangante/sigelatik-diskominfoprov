<?php

namespace App\Charts;

use App\Models\Tickets;
use App\Models\TicketStatuses;
use App\Models\User;
use Carbon\Carbon;

class DinasOverviewService
{
    /**
     * Hitung agregasi overview tiket lintas bidang untuk role Dinas / Helpdesk Utama
     */
    public static function build(): array
    {
        $currentMonth = now()->month;
        $currentYear  = now()->year;

        // 1. Ambil relasi yang pasti ada (service.category, status, perangkatDaerah)
        // Kita hindari memanggil 'assignedUser' di with() agar tidak memicu RelationNotFoundException
        $tickets = Tickets::with([
            'service.category',
            'status',
            'perangkatDaerah',
        ])
        ->whereMonth('created_at', $currentMonth)
        ->whereYear('created_at', $currentYear)
        ->get();

        // Fallback visual jika bulan ini belum ada tiket
        $workingTickets = $tickets->isNotEmpty() 
            ? $tickets 
            : Tickets::with(['service.category', 'status', 'perangkatDaerah'])->get();

        $totalPermohonan = $workingTickets->count();

        // 2. Identifikasi Status Tiket
        $waitingTriageCount = 0;
        $inProcessCount     = 0;
        $resolvedCount      = 0;
        $slaOverdueCount    = 0;

        $totalSlaBidangMinutes  = 0;
        $totalSlaTeknisiMinutes = 0;
        $countResolved          = 0;

        foreach ($workingTickets as $t) {
            $statusCode = strtolower($t->status?->code ?? $t->status?->name ?? '');
            $targetMinutes = (int) ($t->service?->sla_process_minutes ?? $t->service?->sla_minutes ?? 240);
            $totalSlaBidangMinutes += $targetMinutes;

            $createdAt = Carbon::parse($t->created_at);
            $isResolved = str_contains($statusCode, 'selesai') || str_contains($statusCode, 'closed') || str_contains($statusCode, 'solved') || !empty($t->solved_at);

            if ($isResolved) {
                $resolvedCount++;
                $endedAt = !empty($t->solved_at) ? Carbon::parse($t->solved_at) : Carbon::parse($t->updated_at);
                $duration = max(15, $createdAt->diffInMinutes($endedAt));
                $totalSlaTeknisiMinutes += $duration;
                $countResolved++;

                if ($duration > $targetMinutes) {
                    $slaOverdueCount++;
                }
            } elseif (str_contains($statusCode, 'baru') || str_contains($statusCode, 'new') || str_contains($statusCode, 'triage') || str_contains($statusCode, 'pending')) {
                $waitingTriageCount++;
                if ($createdAt->diffInMinutes(now()) > $targetMinutes) {
                    $slaOverdueCount++;
                }
            } else {
                $inProcessCount++;
                if ($createdAt->diffInMinutes(now()) > $targetMinutes) {
                    $slaOverdueCount++;
                }
            }
        }

        // Kalkulasi Rata-rata Durasi SLA (Jam)
        $avgSlaBidangHours = $totalPermohonan > 0 ? round(($totalSlaBidangMinutes / $totalPermohonan) / 60, 1) : 4.0;
        $avgSlaTeknisiHours = $countResolved > 0 ? round(($totalSlaTeknisiMinutes / $countResolved) / 60, 1) : 2.5;

        // 3. Distribusi Tiket per Bidang Layanan
        $groupedBidang = $workingTickets->groupBy(function ($t) {
            return $t->service?->category?->name ?? 'Layanan Umum / TI';
        });

        $bidangProgress = [];
        foreach ($groupedBidang as $name => $items) {
            $count = $items->count();
            $percentage = $totalPermohonan > 0 ? round(($count / $totalPermohonan) * 100) : 0;

            $bidangProgress[] = [
                'name'       => $name,
                'count'      => $count,
                'percentage' => max(4, $percentage),
            ];
        }

        if (empty($bidangProgress)) {
            $bidangProgress = [
                ['name' => 'Layanan Aplikasi', 'count' => 0, 'percentage' => 4],
                ['name' => 'Infrastruktur Jaringan', 'count' => 0, 'percentage' => 4],
                ['name' => 'Keamanan Informasi', 'count' => 0, 'percentage' => 4],
                ['name' => 'Statistik & Data', 'count' => 0, 'percentage' => 4],
            ];
        }

        // 4. Deteksi Nama Teknisi Penangan Tiket secara Dinamis
        $latestTickets = $workingTickets->sortByDesc('created_at')->take(6)->map(function ($t) {
            $statusCode = strtolower($t->status?->code ?? $t->status?->name ?? 'NEW');
            
            if (str_contains($statusCode, 'selesai') || str_contains($statusCode, 'closed')) {
                $badge = ['label' => 'SELESAI', 'bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200', 'dot' => 'bg-emerald-600'];
            } elseif (str_contains($statusCode, 'proses') || str_contains($statusCode, 'progress')) {
                $badge = ['label' => 'PROSES', 'bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'border' => 'border-blue-200', 'dot' => 'bg-blue-600'];
            } elseif (str_contains($statusCode, 'triage') || str_contains($statusCode, 'pending')) {
                $badge = ['label' => 'TRIAGE', 'bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-200', 'dot' => 'bg-amber-600'];
            } else {
                $badge = ['label' => 'BARU', 'bg' => 'bg-red-50', 'text' => 'text-red-700', 'border' => 'border-red-200', 'dot' => 'bg-red-600'];
            }

            // Ambil nama teknisi dari foreign key yang tersedia di tabel tickets tanpa memicu error relasi
            $technicianName = 'Belum Ditugaskan';
            $techId = $t->assigned_to ?? $t->assigned_user_id ?? $t->technician_id ?? $t->user_assigned_id ?? null;
            if ($techId) {
                $techUser = User::find($techId);
                if ($techUser) {
                    $technicianName = $techUser->name;
                }
            }

            return [
                'ticket_number' => $t->ticket_number ?? ('HD-' . Carbon::parse($t->created_at)->format('ym') . '-' . str_pad($t->id, 4, '0', STR_PAD_LEFT)),
                'subject'       => $t->subject ?? $t->service?->name ?? 'Permohonan Layanan',
                'bidang'        => $t->service?->category?->name ?? 'Umum',
                'technician'    => $technicianName,
                'badge'         => $badge,
            ];
        })->values()->toArray();

        return [
            'total_permohonan' => $totalPermohonan,
            'menunggu_triage'  => $waitingTriageCount,
            'dalam_proses'     => $inProcessCount,
            'sla_terlampaui'   => $slaOverdueCount,
            'tiket_selesai'    => $resolvedCount,
            'avg_sla_bidang'   => $avgSlaBidangHours,
            'avg_sla_teknisi'  => $avgSlaTeknisiHours,
            'bidang_progress'  => $bidangProgress,
            'latest_tickets'   => $latestTickets,
        ];
    }
}
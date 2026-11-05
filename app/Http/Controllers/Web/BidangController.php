<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Events\ServiceUpdatedEvent;
use App\Models\Bidang;
use App\Models\Files;
use App\Models\PerangkatDaerah;
use App\Models\ServiceCategory;
use App\Models\Services;
use App\Models\Sops;
use App\Models\SopsStep;
use App\Models\Tickets;
use App\Models\TicketStatuses;
use App\Models\TicketStatusHistory;
use App\Models\TicketTask;
use App\Models\TicketTaskActivity;
use App\Models\TicketTaskAssignees;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BidangController extends Controller
{
    private function formatMinutes(int $minutes): string
    {
        if ($minutes < 60) {
            return "{$minutes} menit";
        } elseif ($minutes % 1440 === 0) {
            return ($minutes / 1440) . " hari";
        } elseif ($minutes % 60 === 0) {
            return ($minutes / 60) . " jam";
        } else {
            $jam = floor($minutes / 60);
            $sisaMenit = $minutes % 60;
            return "{$jam} jam {$sisaMenit} menit";
        }
    }

    private function getActivityModel()
    {
        if (class_exists('App\Models\ServiceActivites')) {
            return app('App\Models\ServiceActivites');
        }
        if (class_exists('App\Models\ServiceActivity')) {
            return app('App\Models\ServiceActivity');
        }
        return null;
    }

    /**
     * Mengambil ID Status Tiket Secara Dinamis (Anti Foreign Key Violation)
     */
    private function getStatusId(string $type): int
    {
        $status = match(strtolower($type)) {
            'diproses', 'process', 'in_progress' => TicketStatuses::where('code', 'diproses')
                ->orWhere('name', 'like', '%proses%')
                ->first(),
            'pending', 'tunda' => TicketStatuses::where('code', 'pending')
                ->orWhere('name', 'like', '%pending%')
                ->orWhere('name', 'like', '%tunda%')
                ->first(),
            'selesai', 'closed', 'resolved' => TicketStatuses::where('code', 'selesai')
                ->orWhere('name', 'like', '%selesai%')
                ->orWhere('name', 'like', '%closed%')
                ->first(),
            default => TicketStatuses::where('code', 'baru')
                ->orWhere('name', 'like', '%baru%')
                ->first(),
        };

        if ($status) {
            return (int) $status->id;
        }

        // Jika belum ada di master data, buat dengan aman dan tangkap ID aslinya
        $newStatus = TicketStatuses::create([
            'name'       => ucfirst($type),
            'code'       => strtolower($type),
            'sort_order' => 1,
        ]);

        return (int) $newStatus->id;
    }

    /**
     * Dashboard / Overview Penanganan Tiket Bidang
     */
    public function index(): View
    {
        $now = now();
        $diprosesId = $this->getStatusId('diproses');
        $pendingId  = $this->getStatusId('pending');
        $baruId     = $this->getStatusId('baru');

        $activeStatusIds = array_unique([$diprosesId, $pendingId]);

        $tickets = Tickets::with(['user', 'status', 'service'])
            ->whereIn('ticket_status_id', $activeStatusIds)
            ->latest('updated_at')
            ->get()
            ->map(function ($ticket) use ($now, $pendingId) {
                $task = TicketTask::where('ticket_id', $ticket->id)->first();
                $techNames = [];
                $assignedTechIds = [];
                $taskMinutes = 0;

                if ($task) {
                    $assignedTechIds = TicketTaskAssignees::where('ticket_task_id', $task->id)
                        ->pluck('user_id')
                        ->toArray();

                    $techNames = User::whereIn('id', $assignedTechIds)->pluck('name')->toArray();

                    $taskMinutes = (int) TicketTaskActivity::where('ticket_task_id', $task->id)
                        ->sum('target_minutes_snapshot');
                }

                $ticket->assigned_technicians = $techNames;
                $ticket->assigned_tech_ids = $assignedTechIds;
                $ticket->task_started_at = $task?->started_at;

                $finalMinutes = $taskMinutes > 0 
                    ? $taskMinutes 
                    : (int) ($ticket->service?->sla_process_minutes ?? $ticket->service?->sla_minutes ?? 240);

                $ticket->target_sla_minutes = $finalMinutes;
                $ticket->target_sla_label = $this->formatMinutes($finalMinutes);

                $startTime = $task?->started_at ? Carbon::parse($task->started_at) : null;

                if ($ticket->ticket_status_id == $pendingId) {
                    $ticket->sla_state = 'paused';
                    $ticket->sla_label = 'SLA Dibekukan';
                    $ticket->sla_color = 'amber';

                    $latestPause = TicketStatusHistory::where('ticket_id', $ticket->id)
                        ->where('ticket_status_id', $pendingId)
                        ->latest('id')
                        ->first();
                    $ticket->pending_note = $latestPause?->note ?? 'SLA penanganan dibekukan sementara.';

                    if ($startTime) {
                        $pauseTime = $ticket->updated_at ? Carbon::parse($ticket->updated_at) : $now;
                        $elapsed = max(0, (int) $startTime->diffInMinutes($pauseTime));
                        $percent = min(100, (int) round(($elapsed / max(1, $finalMinutes)) * 100));
                        $remaining = max(0, $finalMinutes - $elapsed);
                        $ticket->sla_progress = $percent;
                        $ticket->sla_info = $this->formatMinutes($remaining) . ' tersisa (dibekukan)';
                    } else {
                        $ticket->sla_progress = 0;
                        $ticket->sla_info = $ticket->target_sla_label . ' tersisa (dibekukan)';
                    }
                } elseif (!$startTime) {
                    $ticket->sla_state = 'not_started';
                    $ticket->sla_label = 'Belum berjalan';
                    $ticket->sla_color = 'slate';
                    $ticket->sla_progress = 0;
                    $ticket->sla_info = 'Menunggu teknisi mulai';
                } else {
                    $elapsed = max(0, (int) $startTime->diffInMinutes($now));
                    $percent = min(100, (int) round(($elapsed / max(1, $finalMinutes)) * 100));
                    $remaining = max(0, $finalMinutes - $elapsed);
                    $isOverdue = $elapsed >= $finalMinutes;

                    $ticket->sla_progress = $percent;
                    $ticket->is_overdue = $isOverdue;

                    if ($isOverdue) {
                        $ticket->sla_state = 'overdue';
                        $ticket->sla_label = 'Terlampaui (100%)';
                        $ticket->sla_color = 'rose';
                        $ticket->sla_info = '0 menit tersisa';
                    } elseif ($percent >= 80) {
                        $ticket->sla_state = 'critical';
                        $ticket->sla_label = 'Kritis (' . $percent . '%)';
                        $ticket->sla_color = 'rose';
                        $ticket->sla_info = $this->formatMinutes($remaining) . ' tersisa';
                    } else {
                        $ticket->sla_state = 'running';
                        $ticket->sla_label = 'Berjalan (' . $percent . '%)';
                        $ticket->sla_color = 'blue';
                        $ticket->sla_info = $this->formatMinutes($remaining) . ' tersisa';
                    }
                }

                return $ticket;
            });

        $technicians = User::whereHas('roles', function ($q) {
            $q->where('name', 'like', '%teknisi%');
        })->get();

        $ticketMasukCount = Tickets::where('ticket_status_id', $baruId)->count();
        $inProcessCount   = Tickets::where('ticket_status_id', $diprosesId)->count();
        $teknisiPendingCount = Tickets::where('ticket_status_id', $pendingId)->count();
        
        $triagePendingCount = TicketStatusHistory::join('tickets', 'tickets.id', '=', 'ticket_status_histories.ticket_id')
            ->where('tickets.ticket_status_id', $baruId)
            ->where('ticket_status_histories.note', 'like', '%[PAUSED_SLA1]%')
            ->distinct('tickets.id')
            ->count('tickets.id');

        $pendingCount = $teknisiPendingCount + $triagePendingCount;

        $forwardCount = TicketStatusHistory::join('tickets', 'tickets.id', '=', 'ticket_status_histories.ticket_id')
            ->where('ticket_status_histories.note', 'like', 'Forward Tiket:%')
            ->distinct('tickets.id')
            ->count('tickets.id');

        return view('bidang.index', compact(
            'tickets', 
            'technicians', 
            'ticketMasukCount', 
            'inProcessCount', 
            'pendingCount', 
            'forwardCount'
        ));
    }

    /**
     * Penugasan Teknisi & Penyesuaian Durasi SLA (Overview)
     */
    public function assignTeknisi(Request $request, Tickets $ticket): RedirectResponse
    {
        $request->validate([
            'technician_ids'   => 'nullable|array',
            'technician_ids.*' => 'exists:users,id',
            'sla_minutes'      => 'required|numeric|min:5',
        ]);

        $techIds = $request->technician_ids ?? [];
        $hasTechs = !empty($techIds);
        $inputMinutes = (int) $request->sla_minutes;

        $ticket->getConnection()->transaction(function () use ($ticket, $techIds, $hasTechs, $inputMinutes) {
            $now = now();
            $serviceId = $ticket->service_id ?? 1;

            $task = TicketTask::updateOrCreate(
                ['ticket_id' => $ticket->id],
                [
                    'service_id'       => $serviceId,
                    'ticket_status_id' => $ticket->ticket_status_id,
                    'created_by'       => Auth::id() ?? 1,
                    'received_at'      => $now,
                    'started_at'       => $now,
                    'updated_at'       => $now,
                ]
            );

            $taskId = $task->id;

            TicketTaskAssignees::where('ticket_task_id', $taskId)->delete();
            if ($hasTechs) {
                foreach ($techIds as $techId) {
                    TicketTaskAssignees::create([
                        'ticket_task_id' => $taskId,
                        'user_id'        => $techId,
                        'created_at'     => $now,
                        'updated_at'     => $now,
                    ]);
                }
            }

            TicketTaskActivity::where('ticket_task_id', $taskId)->delete();

            $activityModel = $this->getActivityModel();
            $masterActivities = $activityModel 
                ? $activityModel::where('service_id', $serviceId)->orderBy('id', 'asc')->get()
                : collect();

            if ($masterActivities->isNotEmpty()) {
                $totalSteps = $masterActivities->count();
                $minutesPerStep = max(1, (int) round($inputMinutes / $totalSteps));
                $accumulated = 0;

                foreach ($masterActivities as $index => $act) {
                    $stepDuration = ($index === $totalSteps - 1) 
                        ? max(1, $inputMinutes - $accumulated) 
                        : $minutesPerStep;

                    $accumulated += $stepDuration;

                    TicketTaskActivity::create([
                        'ticket_task_id'          => $taskId,
                        'service_activities_id'   => $act->id,
                        'name_snapshot'           => $act->name,
                        'sort_order'              => $index + 1,
                        'target_minutes_snapshot' => $stepDuration,
                        'paused_seconds'          => 0,
                        'is_sla_met'              => null,
                        'start_at'                => null,
                        'due_at'                  => null,
                        'created_at'              => $now,
                        'updated_at'              => $now,
                    ]);
                }
            } else {
                TicketTaskActivity::create([
                    'ticket_task_id'          => $taskId,
                    'service_activities_id'   => null,
                    'name_snapshot'           => 'Penanganan Tiket oleh Teknisi',
                    'sort_order'              => 1,
                    'target_minutes_snapshot' => $inputMinutes,
                    'paused_seconds'          => 0,
                    'is_sla_met'              => null,
                    'start_at'                => null,
                    'due_at'                  => null,
                    'created_at'              => $now,
                    'updated_at'              => $now,
                ]);
            }

            $note = $hasTechs 
                ? "Tiket dialokasikan ke teknisi: " . User::whereIn('id', $techIds)->pluck('name')->join(', ') . ". Target SLA: {$inputMinutes} menit."
                : "Target SLA penanganan tiket diatur ke {$inputMinutes} menit.";

            TicketStatusHistory::create([
                'ticket_id'        => $ticket->id,
                'ticket_task_id'   => $taskId,
                'ticket_status_id' => $ticket->ticket_status_id,
                'changed_by'       => Auth::id() ?? 1,
                'note'             => $note,
            ]);
        });

        $pesan = $hasTechs 
            ? 'Teknisi dan SLA penanganan berhasil diperbarui.' 
            : 'Target SLA tiket berhasil diperbarui.';

        return redirect()->route('bidang.index')->with('success', $pesan);
    }

    /**
     * Halaman Utama Triage
     */
    public function triageIndex(): View
    {
        $now = now();
        $baruId     = $this->getStatusId('baru');
        $diprosesId = $this->getStatusId('diproses');
        $pendingId  = $this->getStatusId('pending');

        $activeStatusIds = array_unique([$diprosesId, $pendingId]);

        // 1. TIKET MENUNGGU TRIAGE
        $waitingTickets = Tickets::with(['user', 'service', 'status', 'perangkatDaerah', 'bidang'])
            ->where('ticket_status_id', $baruId)
            ->latest('created_at')
            ->get()
            ->map(function ($ticket) use ($now) {
                $ticket->agency_name = $ticket->perangkatDaerah?->name ?? 'Perangkat Daerah';

                $catId = $ticket->service?->category_id;
                $ticket->category_name = $catId 
                    ? (ServiceCategory::where('id', $catId)->value('name') ?? 'Belum Ditentukan')
                    : 'Belum Ditentukan';

                $latestHistory = TicketStatusHistory::where('ticket_id', $ticket->id)
                    ->latest('id')
                    ->first();

                $isPaused = $latestHistory && str_contains($latestHistory->note, '[PAUSED_SLA1]');
                $ticket->is_paused = $isPaused;

                $slaTargetMinutes = (int) ($ticket->service?->sla_response_minutes ?? 60);

                $createdAt = $ticket->created_at ? Carbon::parse($ticket->created_at) : $now;
                $elapsedMinutes = (int) $createdAt->diffInMinutes($now);
                $remainingMinutes = max(0, $slaTargetMinutes - $elapsedMinutes);
                $progressPercent = min(100, (int) round(($elapsedMinutes / max(1, $slaTargetMinutes)) * 100));

                $ticket->sla_target_minutes = $slaTargetMinutes;
                $ticket->sla_progress = $progressPercent;
                $ticket->sla_remaining = $remainingMinutes;

                if ($isPaused) {
                    $ticket->sla_status = 'paused';
                    $ticket->sla_label = 'Pending';
                    $ticket->sla_info = 'Luar Jam Kerja';
                    $ticket->sla_color = 'amber';
                } elseif ($elapsedMinutes >= $slaTargetMinutes) {
                    $ticket->sla_status = 'overdue';
                    $ticket->sla_label = 'Terlampaui';
                    $ticket->sla_info = '0 mnt tersisa';
                    $ticket->sla_color = 'rose';
                } elseif ($remainingMinutes <= 15) {
                    $ticket->sla_status = 'critical';
                    $ticket->sla_label = 'Kritis';
                    $ticket->sla_info = "{$remainingMinutes} mnt tersisa";
                    $ticket->sla_color = 'rose';
                } else {
                    $ticket->sla_status = 'running';
                    $ticket->sla_label = 'Berjalan';
                    $ticket->sla_info = "{$remainingMinutes} mnt tersisa";
                    $ticket->sla_color = 'blue';
                }

                return $ticket;
            });

        // 2. TIKET REASSIGN (Tiket aktif yang sudah ditugaskan teknisi)
        $reassignTickets = Tickets::with(['service', 'status', 'user', 'tasks.assignees'])
            ->whereIn('ticket_status_id', $activeStatusIds)
            ->latest('updated_at')
            ->get()
            ->map(function ($ticket) use ($now, $pendingId) {
                $task = TicketTask::where('ticket_id', $ticket->id)->first();
                $currentTechId = null;
                $currentTechName = 'Belum ada teknisi';
                $taskMinutes = 0;

                if ($task) {
                    $currentTechId = TicketTaskAssignees::where('ticket_task_id', $task->id)->value('user_id');
                    if ($currentTechId) {
                        $currentTechName = User::where('id', $currentTechId)->value('name') ?? 'Belum ada teknisi';
                    }

                    $taskMinutes = (int) TicketTaskActivity::where('ticket_task_id', $task->id)
                        ->sum('target_minutes_snapshot');
                }

                $targetSla = $taskMinutes > 0 
                    ? $taskMinutes 
                    : (int) ($ticket->service?->sla_process_minutes ?? $ticket->service?->sla_minutes ?? 240);

                $ticket->target_sla_minutes = $targetSla;
                $ticket->target_sla_label = $this->formatMinutes($targetSla);
                $ticket->current_tech_id = $currentTechId;
                $ticket->current_tech_name = $currentTechName;

                if (!$task || empty($task->started_at)) {
                    $ticket->sla_state = 'not_started';
                    $ticket->sla_progress = 0;
                    $ticket->is_overdue = false;
                    return $ticket;
                }

                $startTime = Carbon::parse($task->started_at);

                if ($ticket->ticket_status_id == $pendingId) {
                    $freezeTime = $ticket->updated_at ? Carbon::parse($ticket->updated_at) : $now;
                    $elapsed = max(0, (int) $startTime->diffInMinutes($freezeTime));
                    $percent = min(100, (int) round(($elapsed / max(1, $targetSla)) * 100));

                    $ticket->sla_state = 'paused';
                    $ticket->sla_progress = $percent;
                    $ticket->is_overdue = $elapsed >= $targetSla;
                    return $ticket;
                }

                $elapsed = max(0, (int) $startTime->diffInMinutes($now));
                $percent = min(100, (int) round(($elapsed / max(1, $targetSla)) * 100));

                $ticket->sla_progress = $percent;
                $ticket->is_overdue = $elapsed >= $targetSla;
                $ticket->sla_state = $ticket->is_overdue ? 'overdue' : 'running';

                return $ticket;
            });

        $categories = ServiceCategory::orderBy('name', 'asc')->get();
        $services = Services::all();
        $bidangs = Bidang::orderBy('name', 'asc')->get();
        $technicians = User::whereHas('roles', function ($q) {
            $q->where('name', 'like', '%teknisi%');
        })->get();

        $newTickets = Tickets::where('ticket_status_id', $baruId)
            ->latest('created_at')
            ->take(5)
            ->get();

        return view('bidang.triage', compact(
            'waitingTickets',
            'reassignTickets',
            'categories',
            'services',
            'bidangs',
            'technicians',
            'newTickets'
        ));
    }

    /**
     * Selesaikan Triage Tiket (Anti Foreign Key Violation)
     */
    public function processTriage(Request $request, Tickets $ticket): RedirectResponse
    {
        $validated = $request->validate([
            'service_id'             => 'required|exists:services,id',
            'additional_sla_minutes' => 'nullable|numeric|min:0',
            'sla_minutes'            => 'nullable|numeric|min:0',
            'technician_ids'         => 'nullable|array',
            'technician_ids.*'       => 'exists:users,id',
        ]);

        // Ambil ID status 'Diproses' yang benar-benar ada di tabel ticket_statuses
        $diprosesId = $this->getStatusId('diproses');

        $ticket->getConnection()->transaction(function () use ($ticket, $validated, $diprosesId) {
            $now = now();
            $newService = Services::findOrFail($validated['service_id']);
            $techIds = $validated['technician_ids'] ?? [];
            $hasTechs = !empty($techIds);

            $baseProcessSla = (int) ($newService->sla_process_minutes ?? $newService->sla_minutes ?? 240);
            $additionalSla = (int) ($validated['additional_sla_minutes'] ?? $validated['sla_minutes'] ?? 0);
            $totalSlaMinutes = $baseProcessSla + $additionalSla;

            // Update status tiket menggunakan ID yang valid
            $ticket->update([
                'service_id'       => $newService->id,
                'ticket_status_id' => $diprosesId,
            ]);

            $task = TicketTask::updateOrCreate(
                ['ticket_id' => $ticket->id],
                [
                    'service_id'       => $newService->id,
                    'ticket_status_id' => $diprosesId,
                    'created_by'       => Auth::id() ?? 1,
                    'received_at'      => $now,
                    'started_at'       => null,
                    'updated_at'       => $now,
                ]
            );

            $taskId = $task->id;

            TicketTaskAssignees::where('ticket_task_id', $taskId)->delete();
            if ($hasTechs) {
                foreach ($techIds as $techId) {
                    TicketTaskAssignees::create([
                        'ticket_task_id' => $taskId,
                        'user_id'        => $techId,
                        'created_at'     => $now,
                        'updated_at'     => $now,
                    ]);
                }
            }

            TicketTaskActivity::where('ticket_task_id', $taskId)->delete();

            $activityModel = $this->getActivityModel();
            $masterActivities = $activityModel 
                ? $activityModel::where('service_id', $newService->id)->orderBy('id', 'asc')->get() 
                : collect();

            if ($masterActivities->isNotEmpty()) {
                $totalSteps = $masterActivities->count();
                $minutesPerStep = max(1, (int) round($totalSlaMinutes / $totalSteps));
                $accumulated = 0;

                foreach ($masterActivities as $index => $act) {
                    $stepDuration = ($index === $totalSteps - 1) 
                        ? max(1, $totalSlaMinutes - $accumulated) 
                        : $minutesPerStep;

                    $accumulated += $stepDuration;

                    TicketTaskActivity::create([
                        'ticket_task_id'          => $taskId,
                        'service_activities_id'   => $act->id,
                        'name_snapshot'           => $act->name,
                        'sort_order'              => $index + 1,
                        'target_minutes_snapshot' => $stepDuration,
                        'paused_seconds'          => 0,
                        'is_sla_met'              => null,
                        'start_at'                => null,
                        'due_at'                  => null,
                        'created_at'              => $now,
                        'updated_at'              => $now,
                    ]);
                }
            } else {
                TicketTaskActivity::create([
                    'ticket_task_id'          => $taskId,
                    'service_activities_id'   => null,
                    'name_snapshot'           => 'Penanganan Tiket oleh Teknisi',
                    'sort_order'              => 1,
                    'target_minutes_snapshot' => $totalSlaMinutes,
                    'paused_seconds'          => 0,
                    'is_sla_met'              => null,
                    'start_at'                => null,
                    'due_at'                  => null,
                    'created_at'              => $now,
                    'updated_at'              => $now,
                ]);
            }

            $techNames = $hasTechs ? User::whereIn('id', $techIds)->pluck('name')->join(', ') : 'Belum ditentukan';
            
            $logNote = $additionalSla > 0
                ? "Triage Selesai: Sub-layanan '{$newService->name}'. SLA Standar: {$baseProcessSla} mnt (+Tambahan Disetujui: {$additionalSla} mnt). Total SLA: {$totalSlaMinutes} menit. Teknisi: {$techNames}."
                : "Triage Selesai: Sub-layanan '{$newService->name}'. SLA Proses Otomatis: {$totalSlaMinutes} menit. Teknisi: {$techNames}.";

            TicketStatusHistory::create([
                'ticket_id'        => $ticket->id,
                'ticket_task_id'   => $taskId,
                'ticket_status_id' => $diprosesId,
                'changed_by'       => Auth::id() ?? 1,
                'note'             => $logNote,
            ]);
        });

        return redirect()->route('bidang.triage')->with('success', "Triage tiket {$ticket->ticket_number} berhasil diselesaikan.");
    }

    /**
     * Jeda (Pause) SLA Respon 1
     */
    public function pendingTriage(Request $request, Tickets $ticket): RedirectResponse
    {
        $validated = $request->validate([
            'pending_reason' => 'required|string',
            'pending_note'   => 'nullable|string|max:500',
        ]);

        $reasonLabels = [
            'outside_office_hours'          => 'Di luar jam operasional kerja',
            'waiting_user_attachment'       => 'Menunggu kelengkapan dokumen/bukti dari OPD',
            'cross_department_coordination' => 'Koordinasi lintas bidang',
            'other'                         => 'Alasan lainnya',
        ];

        $reasonText = $reasonLabels[$validated['pending_reason']] ?? $validated['pending_reason'];
        $fullNote = "[PAUSED_SLA1] SLA Respon dijeda. Alasan: {$reasonText}.";
        if (!empty($validated['pending_note'])) {
            $fullNote .= " Catatan: {$validated['pending_note']}";
        }

        TicketStatusHistory::create([
            'ticket_id'        => $ticket->id,
            'ticket_task_id'   => null,
            'ticket_status_id' => $ticket->ticket_status_id,
            'changed_by'       => Auth::id() ?? 1,
            'note'             => $fullNote,
        ]);

        return redirect()->route('bidang.triage')->with('success', "SLA Respon tiket {$ticket->ticket_number} berhasil dibekukan.");
    }

    /**
     * Reassign Tiket ke Teknisi Lain
     */
    public function reassignTeknisi(Request $request, Tickets $ticket): RedirectResponse
    {
        $techId = $request->technician_id ?? $request->target_bidang_id;

        if (!$techId || !User::where('id', $techId)->exists()) {
            return back()->withErrors(['technician_id' => 'Pilih salah satu teknisi yang valid.']);
        }

        $diprosesId = $this->getStatusId('diproses');

        $ticket->getConnection()->transaction(function () use ($ticket, $techId, $diprosesId) {
            $now = now();
            
            $task = TicketTask::firstOrCreate(
                ['ticket_id' => $ticket->id],
                [
                    'service_id'       => $ticket->service_id ?? 1,
                    'ticket_status_id' => $diprosesId,
                    'created_by'       => Auth::id() ?? 1,
                    'received_at'      => $now,
                    'started_at'       => null,
                    'created_at'       => $now,
                    'updated_at'       => $now,
                ]
            );

            $oldTechId = TicketTaskAssignees::where('ticket_task_id', $task->id)->value('user_id');
            $oldTechName = $oldTechId ? (User::find($oldTechId)?->name ?? 'Teknisi Sebelumnya') : 'Belum ada';

            TicketTaskAssignees::where('ticket_task_id', $task->id)->delete();
            TicketTaskAssignees::create([
                'ticket_task_id' => $task->id,
                'user_id'        => $techId,
                'created_at'     => $now,
                'updated_at'     => $now,
            ]);

            $newTechName = User::find($techId)?->name ?? 'Teknisi Baru';

            TicketStatusHistory::create([
                'ticket_id'        => $ticket->id,
                'ticket_task_id'   => $task->id,
                'ticket_status_id' => $diprosesId,
                'changed_by'       => Auth::id() ?? 1,
                'note'             => "Reassign Teknisi: Penanganan dialihkan dari {$oldTechName} ke {$newTechName}.",
            ]);

            $ticket->touch();
        });

        return back()->with('success', "Penugasan tiket {$ticket->ticket_number} berhasil dialihkan.");
    }

    /**
     * Forward Tiket ke Bidang Lain
     */
    public function forwardTicket(Request $request, Tickets $ticket): RedirectResponse
    {
        $validated = $request->validate([
            'target_bidang_id' => 'required',
            'forward_note'     => 'nullable|string|max:500',
        ]);

        $targetBidang = Bidang::find($validated['target_bidang_id']);
        $bidangName = $targetBidang->name ?? 'Bidang Terkait';
        $baruId = $this->getStatusId('baru');

        $ticket->getConnection()->transaction(function () use ($ticket, $validated, $bidangName, $baruId) {
            $now = now();

            $ticket->update([
                'ticket_status_id' => $baruId,
                'bidang_id'        => $validated['target_bidang_id'],
            ]);

            $task = TicketTask::where('ticket_id', $ticket->id)->first();
            if ($task) {
                TicketTaskAssignees::where('ticket_task_id', $task->id)->delete();
                $task->update([
                    'ticket_status_id' => $baruId,
                    'updated_at'       => $now,
                ]);
            }

            $noteText = "Forward Tiket: Aduan diteruskan ke {$bidangName}.";
            if (!empty($validated['forward_note'])) {
                $noteText .= " Alasan/Catatan: {$validated['forward_note']}";
            }

            TicketStatusHistory::create([
                'ticket_id'        => $ticket->id,
                'ticket_task_id'   => $task?->id,
                'ticket_status_id' => $baruId,
                'changed_by'       => Auth::id() ?? 1,
                'note'             => $noteText,
            ]);
        });

        return redirect()->route('bidang.triage')->with('success', "Tiket {$ticket->ticket_number} berhasil diteruskan (Forward) ke {$bidangName}.");
    }

    public function reassignBidang(Request $request, Tickets $ticket): RedirectResponse
    {
        if ($request->has('technician_id') || $request->filled('technician_id')) {
            return $this->reassignTeknisi($request, $ticket);
        }

        return $this->forwardTicket($request, $ticket);
    }

    /**
     * Halaman Kategori & SLA Bidang
     */
    public function kategori(): View
    {
        $sops = Sops::with('steps')->get();
        $activityModel = $this->getActivityModel();

        $categories = ServiceCategory::all()->map(function ($cat) use ($activityModel) {
            $services = Services::where('category_id', $cat->id)
                ->get()
                ->map(function ($svc) use ($activityModel) {
                    $respMinutes = $svc->sla_response_minutes ?? 60;
                    $svc->sla_response_val = (int) $respMinutes;
                    $svc->sla_response_display = $this->formatMinutes((int) $respMinutes);

                    $procMinutes = $svc->sla_process_minutes ?? $svc->sla_minutes ?? 240;
                    $svc->sla_minutes_val = (int) $procMinutes;
                    $svc->sla_display = $this->formatMinutes((int) $procMinutes);

                    $activities = $activityModel 
                        ? $activityModel::where('service_id', $svc->id)->get() 
                        : collect();

                    $svc->activities = $activities;
                    return $svc;
                });

            $cat->services = $services;
            return $cat;
        });

        return view('bidang.kategori', compact('categories', 'sops'));
    }

    public function storeActivity(Request $request, $serviceId): RedirectResponse
    {
        $validated = $request->validate(['name' => 'required|string|max:255']);
        $activityModel = $this->getActivityModel();

        if ($activityModel) {
            $cols = $activityModel->getConnection()->getSchemaBuilder()->getColumnListing($activityModel->getTable());
            $actData = ['service_id' => $serviceId, 'name' => $validated['name']];

            if (in_array('target_minutes', $cols)) $actData['target_minutes'] = 0;
            if (in_array('duration_minutes', $cols)) $actData['duration_minutes'] = 0;

            $activityModel::create($actData);
        }

        return redirect()->route('bidang.kategori')->with('success', 'Aktivitas SOP berhasil ditambahkan.');
    }

    public function storeCategory(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        $catData = [
            'name' => $validated['name'],
        ];

        $catModel = new ServiceCategory();
        $cols = $catModel->getConnection()->getSchemaBuilder()->getColumnListing($catModel->getTable());
        if (in_array('description', $cols)) {
            $catData['description'] = $validated['description'] ?? "Kategori {$validated['name']}";
        }

        ServiceCategory::create($catData);

        return redirect()->route('bidang.kategori')->with('success', "Kategori '{$validated['name']}' berhasil ditambahkan.");
    }

    /**
     * Tambah Layanan Baru dengan SLA Dinamis
     */
    public function storeService(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id'   => 'required|exists:service_categories,id',
            'name'          => 'required|string|max:255',
            'description'   => 'nullable|string',
            'sla_minutes'   => 'required|array|min:2',
            'sla_minutes.*' => 'required|numeric|min:5',
            'work_hours'    => 'nullable|string|max:100',
        ], [
            'sla_minutes.required' => 'Input SLA wajib diisi.',
            'sla_minutes.min'      => 'Minimal input harus 2 tahapan SLA (SLA 1 dan SLA 2).',
            'sla_minutes.*.min'    => 'Setiap durasi SLA minimal 5 menit.',
        ]);

        $slas = array_map('intval', $validated['sla_minutes']);
        
        $slaResponse = $slas[0];
        $slaProcess = array_sum(array_slice($slas, 1));

        $svc = new Services();
        $cols = $svc->getConnection()->getSchemaBuilder()->getColumnListing($svc->getTable());

        $svcData = [
            'category_id' => $validated['category_id'],
            'name'        => $validated['name'],
            'description' => $validated['description'] ?? 'Layanan bidang',
        ];

        if (in_array('sla_response_minutes', $cols)) {
            $svcData['sla_response_minutes'] = $slaResponse;
        }

        if (in_array('sla_process_minutes', $cols)) {
            $svcData['sla_process_minutes'] = $slaProcess;
        }

        if (in_array('sla_minutes', $cols)) {
            $svcData['sla_minutes'] = $slaProcess;
        }

        if (in_array('sla_hours', $cols)) {
            $svcData['sla_hours'] = round($slaProcess / 60, 2);
        }

        if (in_array('code', $cols)) $svcData['code'] = rand(100, 999);
        if (in_array('work_hours', $cols)) $svcData['work_hours'] = $validated['work_hours'] ?? '08:00 - 15:00 WITA';

        Services::create($svcData);

        $totalStages = count($slas);
        return redirect()->route('bidang.kategori')->with('success', "Layanan '{$validated['name']}' berhasil ditambahkan dengan {$totalStages} tahapan SLA dinamis.");
    }

    /**
     * Update Target SLA Layanan
     */
    public function updateSla(Request $request, $serviceId): RedirectResponse
    {
        $validated = $request->validate([
            'sla_minutes' => 'required|numeric|min:5',
            'work_hours'  => 'required|string|max:100',
        ]);

        $service = Services::findOrFail($serviceId);
        $cols = $service->getConnection()->getSchemaBuilder()->getColumnListing($service->getTable());
        $updateData = [];

        $processMin = (int) $validated['sla_minutes'];
        if (in_array('sla_process_minutes', $cols)) $updateData['sla_process_minutes'] = $processMin;
        if (in_array('sla_minutes', $cols)) $updateData['sla_minutes'] = $processMin;
        if (in_array('sla_hours', $cols)) $updateData['sla_hours'] = round($processMin / 60, 2);

        if (in_array('work_hours', $cols)) $updateData['work_hours'] = $validated['work_hours'];

        if (!empty($updateData)) {
            $service->update($updateData);
        }

        return redirect()->route('bidang.kategori')->with('success', 'Target SLA layanan berhasil diperbarui.');
    }

    /**
     * Halaman SOP
     */
    public function sopIndex(): View
    {
        $sops = Sops::with(['service', 'file', 'creator', 'steps'])
            ->latest('id')
            ->get();

        $services = Services::all();

        return view('bidang.sop', compact('sops', 'services'));
    }

    public function viewPdf($slug)
    {
        $possibleId = is_numeric($slug) ? (int) $slug : (int) explode('-', $slug)[0];

        $sop = Sops::with('file')
            ->where('slug', $slug)
            ->when($possibleId > 0, function ($q) use ($possibleId) {
                return $q->orWhere('id', $possibleId);
            })
            ->first();

        if (!$sop) {
            abort(404, 'Data dokumen SOP tidak ditemukan.');
        }

        $path = $sop->file?->path ?? $sop->file_path;

        if (!$path || !Storage::disk('public')->exists($path)) {
            abort(404, "File fisik PDF tidak ditemukan pada path: storage/app/public/{$path}");
        }

        return response()->file(Storage::disk('public')->path($path), [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . ($sop->file?->original_name ?? 'dokumen.pdf') . '"'
        ]);
    }

    public function storeSop(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'service_id'  => 'required|exists:services,id',
            'name'        => 'required|string|max:255',
            'doc_number'  => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'file'        => 'required|file|mimes:pdf|max:10240',
        ]);

        $uploaded = $request->file('file');
        $disk = 'public';
        $path = $uploaded->store('sops', $disk);

        $fileRecord = Files::create([
            'disk'            => $disk,
            'path'            => $path,
            'original_name'   => $uploaded->getClientOriginalName(),
            'mime_type'       => $uploaded->getClientMimeType(),
            'size_bytes'      => $uploaded->getSize(),
            'checksum_sha256' => hash_file('sha256', $uploaded->getRealPath()),
            'uploaded_by'     => Auth::id() ?? 1,
            'created_at'      => now(),
        ]);

        $latestVersion = Sops::where('service_id', $validated['service_id'])->max('version') ?? 0;
        $version = $latestVersion + 1;

        $baseSlug = Str::slug($validated['name']) . '-v' . $version;
        $slug = $baseSlug;
        $counter = 1;
        while (Sops::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }

        Sops::create([
            'name'        => $validated['name'],
            'title'       => $validated['name'],
            'slug'        => $slug,
            'service_id'  => $validated['service_id'],
            'file_id'     => $fileRecord->id,
            'version'     => $version,
            'created_by'  => Auth::id() ?? 1,
            'doc_number'  => $validated['doc_number'],
            'description' => $validated['description'],
            'file_path'   => $path,
        ]);

        return redirect()->route('bidang.sop')->with('success', "Dokumen SOP '{$validated['name']}' berhasil disimpan.");
    }

    public function updateSop(Request $request, $id): RedirectResponse
    {
        $validated = $request->validate([
            'service_id'  => 'nullable|exists:services,id',
            'name'        => 'required|string|max:255',
            'doc_number'  => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'file'        => 'nullable|file|mimes:pdf|max:10240',
        ]);

        $sop = Sops::findOrFail($id);
        $data = [
            'name'        => $validated['name'],
            'title'       => $validated['name'],
            'doc_number'  => $validated['doc_number'],
            'description' => $validated['description'],
        ];

        if (!empty($validated['service_id'])) {
            $data['service_id'] = $validated['service_id'];
        }

        if ($request->hasFile('file')) {
            $uploaded = $request->file('file');
            $disk = 'public';
            $path = $uploaded->store('sops', $disk);

            $fileRecord = Files::create([
                'disk'            => $disk,
                'path'            => $path,
                'original_name'   => $uploaded->getClientOriginalName(),
                'mime_type'       => $uploaded->getClientMimeType(),
                'size_bytes'      => $uploaded->getSize(),
                'checksum_sha256' => hash_file('sha256', $uploaded->getRealPath()),
                'uploaded_by'     => Auth::id() ?? 1,
                'created_at'      => now(),
            ]);

            $newVersion = ($sop->version ?? 1) + 1;
            $data['file_id'] = $fileRecord->id;
            $data['version'] = $newVersion;
            $data['slug'] = Str::slug($validated['name']) . '-v' . $newVersion;
            $data['file_path'] = $path;
        }

        $sop->update($data);

        return redirect()->route('bidang.sop')->with('success', 'Dokumen SOP berhasil diperbarui.');
    }

    public function storeSopStep(Request $request, $id): RedirectResponse
    {
        $validated = $request->validate(['name' => 'required|string|max:255']);
        $sop = Sops::findOrFail($id);
        $nextStepNo = (SopsStep::where('sop_id', $sop->id)->max('step_no') ?? 0) + 1;

        SopsStep::create([
            'sop_id'      => $sop->id,
            'step_no'     => $nextStepNo,
            'description' => $validated['name'],
        ]);

        return redirect()->route('bidang.sop')->with('success', "Langkah ke-{$nextStepNo} berhasil ditambahkan ke SOP.");
    }

    public function updateSopStep(Request $request, $stepId): RedirectResponse
    {
        $validated = $request->validate(['name' => 'required|string|max:255']);
        $step = SopsStep::findOrFail($stepId);
        $step->update(['description' => $validated['name']]);

        return redirect()->route('bidang.sop')->with('success', 'Tahapan SOP berhasil diperbarui.');
    }

    public function destroySopStep($stepId): RedirectResponse
    {
        $step = SopsStep::findOrFail($stepId);
        $sopId = $step->sop_id;
        $deletedStepNo = $step->step_no;

        $step->delete();

        SopsStep::where('sop_id', $sopId)
            ->where('step_no', '>', $deletedStepNo)
            ->decrement('step_no');

        return redirect()->route('bidang.sop')->with('success', 'Tahapan SOP berhasil dihapus.');
    }

    /**
     * Tambah Ekstensi Durasi SLA pada Tiket
     */
    public function tambahSla(Request $request, Tickets $ticket): RedirectResponse
    {
        $validated = $request->validate([
            'additional_minutes' => 'required|numeric|min:5',
            'reason'             => 'nullable|string|max:500',
        ], [
            'additional_minutes.required' => 'Masukkan durasi menit tambahan SLA.',
            'additional_minutes.min'      => 'Durasi penambahan SLA minimal 5 menit.',
        ]);

        $addedMinutes = (int) $validated['additional_minutes'];
        $reason = $validated['reason'] ?? 'Penambahan durasi waktu penanganan tiket';

        $ticket->getConnection()->transaction(function () use ($ticket, $addedMinutes, $reason) {
            $now = now();
            $task = TicketTask::where('ticket_id', $ticket->id)->first();

            if (!$task) {
                $baseProcessSla = (int) ($ticket->service?->sla_process_minutes ?? $ticket->service?->sla_minutes ?? 240);
                $task = TicketTask::create([
                    'ticket_id'        => $ticket->id,
                    'service_id'       => $ticket->service_id ?? 1,
                    'ticket_status_id' => $ticket->ticket_status_id,
                    'created_by'       => Auth::id() ?? 1,
                    'received_at'      => $now,
                    'started_at'       => null,
                    'created_at'       => $now,
                    'updated_at'       => $now,
                ]);

                TicketTaskActivity::create([
                    'ticket_task_id'          => $task->id,
                    'service_activities_id'   => null,
                    'name_snapshot'           => 'Penanganan Tiket oleh Teknisi',
                    'sort_order'              => 1,
                    'target_minutes_snapshot' => $baseProcessSla,
                    'paused_seconds'          => 0,
                    'is_sla_met'              => null,
                    'created_at'              => $now,
                    'updated_at'              => $now,
                ]);
            } else {
                $hasActivities = TicketTaskActivity::where('ticket_task_id', $task->id)->exists();
                if (!$hasActivities) {
                    $baseProcessSla = (int) ($ticket->service?->sla_process_minutes ?? $ticket->service?->sla_minutes ?? 240);
                    TicketTaskActivity::create([
                        'ticket_task_id'          => $task->id,
                        'service_activities_id'   => null,
                        'name_snapshot'           => 'Penanganan Tiket oleh Teknisi',
                        'sort_order'              => 1,
                        'target_minutes_snapshot' => $baseProcessSla,
                        'paused_seconds'          => 0,
                        'is_sla_met'              => null,
                        'created_at'              => $now,
                        'updated_at'              => $now,
                    ]);
                }
            }

            $nextSort = (TicketTaskActivity::where('ticket_task_id', $task->id)->max('sort_order') ?? 0) + 1;

            TicketTaskActivity::create([
                'ticket_task_id'          => $task->id,
                'service_activities_id'   => null,
                'name_snapshot'           => "Penambahan Waktu SLA (+{$addedMinutes} Menit)",
                'sort_order'              => $nextSort,
                'target_minutes_snapshot' => $addedMinutes,
                'paused_seconds'          => 0,
                'is_sla_met'              => null,
                'created_at'              => $now,
                'updated_at'              => $now,
            ]);

            $task->touch();

            TicketStatusHistory::create([
                'ticket_id'        => $ticket->id,
                'ticket_task_id'   => $task->id,
                'ticket_status_id' => $ticket->ticket_status_id,
                'changed_by'       => Auth::id() ?? 1,
                'note'             => "[SLA_EXTENDED] Penambahan SLA sebesar {$addedMinutes} menit. Alasan/Catatan: {$reason}",
            ]);

            $ticket->touch();
        });

        return redirect()->route('bidang.triage')->with('success', "Target SLA tiket {$ticket->ticket_number} berhasil ditambah {$addedMinutes} menit.");
    }

    public function destroyService($id)
    {
        $service = Services::withCount('tickets')->findOrFail($id);

        if ($service->tickets_count > 0) {
            broadcast(new ServiceUpdatedEvent("Sub-layanan '{$service->name}' gagal dihapus karena memiliki tiket aktif.", 'error'));
            return back();
        }

        $serviceName = $service->name;
        $service->delete();

        // Broadcast ke Reverb
        broadcast(new ServiceUpdatedEvent("Sub-layanan '{$serviceName}' berhasil dihapus.", 'success'));

        return back();
    }

    public function destroyCategory($id)
    {
        $category = ServiceCategory::withCount('services')->findOrFail($id);

        if ($category->services_count > 0) {
            broadcast(new ServiceUpdatedEvent("Kategori '{$category->name}' gagal dihapus karena masih memuat layanan.", 'error'));
            return back();
        }

        $catName = $category->name;
        $category->delete();

        // Broadcast ke Reverb
        broadcast(new ServiceUpdatedEvent("Kategori '{$catName}' berhasil dihapus.", 'success'));

        // 2. Beri flash session (agar user yang baru saja klik hapus langsung melihat toast dari b-master)
        return back()->with('success', "Kategori '{$catName}' berhasil dihapus.");
    }
}
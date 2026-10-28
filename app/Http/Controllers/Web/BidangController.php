<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Tickets;
use App\Models\User;
use App\Models\TicketTask;
use App\Models\TicketStatusHistory;
use App\Models\Sops;
use App\Models\SopsStep;
use App\Models\Services;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BidangController extends Controller
{
    /**
     * Helper pemformat menit ke label teks
     */
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

    /**
     * Halaman Overview & Monitoring Tiket Bidang
     * Menampilkan tiket yang sudah melalui Triage (Status: 2 / Diproses & 3 / Dijeda)
     */
    /**
     * Halaman Overview & Monitoring Tiket Bidang
     */
/**
     * Halaman Overview & Monitoring Tiket Bidang
     */
    /**
     * Halaman Overview & Monitoring Tiket Bidang
     */
    public function index(): View
    {
        $now = now();

        // 1. Ambil tiket aktif bidang (Diproses: 2, Dijeda/Pending: 3)
        $tickets = Tickets::with(['user', 'status', 'service'])
            ->whereIn('ticket_status_id', [2, 3])
            ->latest('updated_at')
            ->get()
            ->map(function ($ticket) use ($now) {
                $task = DB::table('tickets_task')->where('ticket_id', $ticket->id)->first();
                $techNames = [];
                $assignedTechIds = [];
                $taskMinutes = 0;

                if ($task) {
                    $assignedTechIds = DB::table('ticket_task_assignees')
                        ->where('ticket_task_id', $task->id)
                        ->pluck('user_id')
                        ->toArray();

                    $techNames = User::whereIn('id', $assignedTechIds)->pluck('name')->toArray();

                    $taskMinutes = (int) DB::table('ticket_task_activities')
                        ->where('ticket_task_id', $task->id)
                        ->sum('target_minutes_snapshot');
                }

                $ticket->assigned_technicians = $techNames;
                $ticket->assigned_tech_ids = $assignedTechIds;
                $ticket->task_started_at = $task?->started_at;

                $finalMinutes = $taskMinutes > 0 
                    ? $taskMinutes 
                    : (int) ($ticket->service?->sla_minutes ?? 240);

                $ticket->target_sla_minutes = $finalMinutes;
                $ticket->target_sla_label = $this->formatMinutes($finalMinutes);

                $startTime = $task?->started_at ? Carbon::parse($task->started_at) : null;

                // KONDISI 1: TIKET STATUS 3 (PENDING / DIJEDA TEKNISI)
                // Wajib tampil sebagai SLA Dibekukan, baik teknisi sudah klik mulai atau belum
                if ($ticket->ticket_status_id == 3) {
                    $ticket->sla_state = 'paused';
                    $ticket->sla_label = 'SLA Dibekukan';
                    $ticket->sla_color = 'amber';

                    $latestPause = DB::table('ticket_status_histories')
                        ->where('ticket_id', $ticket->id)
                        ->where('ticket_status_id', 3)
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
                    // KONDISI 2: DIPROSES TAPI BELUM MULAI KERJA
                    $ticket->sla_state = 'not_started';
                    $ticket->sla_label = 'Belum berjalan';
                    $ticket->sla_color = 'slate';
                    $ticket->sla_progress = 0;
                    $ticket->sla_info = 'Menunggu teknisi mulai';
                } else {
                    // KONDISI 3: DIPROSES & BERJALAN NORMAL
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
            })
            ->get();

        // 1. Tiket Masuk (Status 1: Baru)
        $ticketMasukCount = Tickets::where('ticket_status_id', 1)->count();

        // 2. Tiket Sedang Diproses (Status 2)
        $inProcessCount = Tickets::where('ticket_status_id', 2)->count();

        // 3. Bekukan SLA: Gabungan Status 3 (Pending Teknisi) + Status 1 yang dibekukan di Triage
        $teknisiPendingCount = Tickets::where('ticket_status_id', 3)->count();
        $triagePendingCount = 0;
        if (Schema::hasTable('ticket_status_histories')) {
            $triagePendingCount = DB::table('ticket_status_histories')
                ->join('tickets', 'tickets.id', '=', 'ticket_status_histories.ticket_id')
                ->where('tickets.ticket_status_id', 1)
                ->where('ticket_status_histories.note', 'like', '%[PAUSED_SLA1]%')
                ->distinct('tickets.id')
                ->count('tickets.id');
        }
        $pendingCount = $teknisiPendingCount + $triagePendingCount;

        // 4. Tiket Forward Lintas Bidang
        $forwardCount = 0;
        if (Schema::hasTable('ticket_status_histories')) {
            $forwardCount = DB::table('ticket_status_histories')
                ->where('note', 'like', 'Forward Tiket:%')
                ->distinct('ticket_id')
                ->count('ticket_id');
        }

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

        DB::transaction(function () use ($ticket, $techIds, $hasTechs, $inputMinutes) {
            $now = now();
            $serviceId = $ticket->service_id ?? 1;

            $taskId = DB::table('tickets_task')->where('ticket_id', $ticket->id)->value('id');

            $taskData = [
                'ticket_id'        => $ticket->id,
                'service_id'       => $serviceId,
                'ticket_status_id' => $ticket->ticket_status_id,
                'updated_at'       => $now,
            ];

            if (!$taskId) {
                $taskData['created_by']  = Auth::id() ?? 1;
                $taskData['received_at'] = $now;
                $taskData['started_at']  = $now;
                $taskData['created_at']  = $now;
                $taskId = DB::table('tickets_task')->insertGetId($taskData);
            } else {
                DB::table('tickets_task')->where('id', $taskId)->update($taskData);
            }

            DB::table('ticket_task_assignees')->where('ticket_task_id', $taskId)->delete();
            if ($hasTechs) {
                foreach ($techIds as $techId) {
                    DB::table('ticket_task_assignees')->insert([
                        'ticket_task_id' => $taskId,
                        'user_id'        => $techId,
                        'created_at'     => $now,
                        'updated_at'     => $now,
                    ]);
                }
            }

            DB::table('ticket_task_activities')->where('ticket_task_id', $taskId)->delete();

            $masterActivities = DB::table('service_activities')
                ->where('service_id', $serviceId)
                ->orderBy('id', 'asc')
                ->get();

            if ($masterActivities->isNotEmpty()) {
                $totalSteps = $masterActivities->count();
                $minutesPerStep = max(1, (int) round($inputMinutes / $totalSteps));
                $accumulated = 0;

                foreach ($masterActivities as $index => $act) {
                    $stepDuration = ($index === $totalSteps - 1) 
                        ? ($inputMinutes - $accumulated) 
                        : $minutesPerStep;

                    $accumulated += $stepDuration;

                    DB::table('ticket_task_activities')->insert([
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
                DB::table('ticket_task_activities')->insert([
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

            if ($hasTechs) {
                $techNames = User::whereIn('id', $techIds)->pluck('name')->join(', ');
                TicketStatusHistory::create([
                    'ticket_id'        => $ticket->id,
                    'ticket_task_id'   => $taskId,
                    'ticket_status_id' => $ticket->ticket_status_id,
                    'changed_by'       => Auth::id() ?? 1,
                    'note'             => "Tiket dialokasikan ke teknisi: {$techNames}.",
                ]);
            } else {
                TicketStatusHistory::create([
                    'ticket_id'        => $ticket->id,
                    'ticket_task_id'   => $taskId,
                    'ticket_status_id' => $ticket->ticket_status_id,
                    'changed_by'       => Auth::id() ?? 1,
                    'note'             => "Target SLA tiket diatur ke {$inputMinutes} menit.",
                ]);
            }
        });

        $pesan = $hasTechs 
            ? 'Teknisi dan SLA penanganan berhasil diperbarui.' 
            : 'Target SLA tiket berhasil diperbarui.';

        return redirect()->route('bidang.index')->with('success', $pesan);
    }

    /**
     * Halaman Utama Triage & Reassign
     */
    public function triageIndex(): View
    {
        $now = now();

        // 1. Data Tiket Menunggu Triage (Status: Baru / ID 1)
        $waitingTickets = Tickets::with(['user', 'service', 'status'])
            ->where('ticket_status_id', 1)
            ->latest('created_at')
            ->get()
            ->map(function ($ticket) use ($now) {
                $ticket->agency_name = DB::table('perangkat_daerah')
                    ->where('id', function ($query) use ($ticket) {
                        $query->select('perangkat_daerah_id')
                            ->from('employments')
                            ->where('user_id', $ticket->user_id)
                            ->limit(1);
                    })->value('name') ?? 'Perangkat Daerah';

                $catId = $ticket->service?->category_id;
                $ticket->category_name = $catId 
                    ? (DB::table('service_categories')->where('id', $catId)->value('name') ?? 'Belum Ditentukan')
                    : 'Belum Ditentukan';

                $latestHistory = DB::table('ticket_status_histories')
                    ->where('ticket_id', $ticket->id)
                    ->latest('id')
                    ->first();

                $isPaused = $latestHistory && str_contains($latestHistory->note, '[PAUSED_SLA1]');
                $ticket->is_paused = $isPaused;

                $slaTargetMinutes = 60;
                $createdAt = $ticket->created_at ? Carbon::parse($ticket->created_at) : $now;
                $elapsedMinutes = (int) $createdAt->diffInMinutes($now);
                $remainingMinutes = max(0, $slaTargetMinutes - $elapsedMinutes);
                $progressPercent = min(100, (int) round(($elapsedMinutes / $slaTargetMinutes) * 100));

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

        // 2. Data Tiket Reassign Antar Teknisi Bidang (Status: Diproses / ID 2 & Dijeda / ID 3)
        $reassignTickets = Tickets::with(['service', 'status'])
            ->whereIn('ticket_status_id', [2, 3])
            ->latest('updated_at')
            ->get()
            ->map(function ($ticket) use ($now) {
                $targetSla = (int) ($ticket->service?->sla_minutes ?? 240);
                $ticket->target_sla_label = $this->formatMinutes($targetSla);

                $task = DB::table('tickets_task')->where('ticket_id', $ticket->id)->first();
                $currentTechId = null;
                $currentTechName = 'Belum ada teknisi';

                if ($task) {
                    $currentTechId = DB::table('ticket_task_assignees')
                        ->where('ticket_task_id', $task->id)
                        ->value('user_id');

                    if ($currentTechId) {
                        $currentTechName = User::where('id', $currentTechId)->value('name') ?? 'Belum ada teknisi';
                    }
                }

                $ticket->current_tech_id = $currentTechId;
                $ticket->current_tech_name = $currentTechName;

                // KONDISI 1: Teknisi belum menekan 'Mulai Proses' (started_at masih NULL)
                if (!$task || empty($task->started_at)) {
                    $ticket->sla_state = 'not_started';
                    $ticket->sla_progress = 0;
                    $ticket->is_overdue = false;
                    return $ticket;
                }

                $startTime = Carbon::parse($task->started_at);

                // KONDISI 2: Tiket sedang berstatus PENDING / DIJEDA (Status 3) -> Timer dibekukan
                if ($ticket->ticket_status_id == 3) {
                    $freezeTime = $ticket->updated_at ? Carbon::parse($ticket->updated_at) : $now;
                    $elapsed = max(0, (int) $startTime->diffInMinutes($freezeTime));
                    $percent = min(100, (int) round(($elapsed / max(1, $targetSla)) * 100));

                    $ticket->sla_state = 'paused';
                    $ticket->sla_progress = $percent;
                    $ticket->is_overdue = $elapsed >= $targetSla;
                    return $ticket;
                }

                // KONDISI 3: Tiket sedang BERJALAN (Status 2)
                $elapsed = max(0, (int) $startTime->diffInMinutes($now));
                $percent = min(100, (int) round(($elapsed / max(1, $targetSla)) * 100));

                $ticket->sla_progress = $percent;
                $ticket->is_overdue = $elapsed >= $targetSla;
                $ticket->sla_state = $ticket->is_overdue ? 'overdue' : 'running';

                return $ticket;
            });

        $categories = DB::table('service_categories')->get();
        $services = Services::all();
        $bidangs = DB::table('bidang')->get();
        $technicians = User::whereHas('roles', function ($q) {
            $q->where('name', 'like', '%teknisi%');
        })->get();

        $newTickets = Tickets::where('ticket_status_id', 1)
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
     * Selesaikan Triage: started_at bernilai NULL agar SLA belum berjalan sebelum teknisi mulai
     */
    public function processTriage(Request $request, Tickets $ticket): RedirectResponse
    {
        $validated = $request->validate([
            'service_id'       => 'required|exists:services,id',
            'sla_minutes'      => 'required|numeric|min:5',
            'technician_ids'   => 'nullable|array',
            'technician_ids.*' => 'exists:users,id',
        ]);

        DB::transaction(function () use ($ticket, $validated) {
            $now = now();
            $newService = Services::findOrFail($validated['service_id']);
            $techIds = $validated['technician_ids'] ?? [];
            $hasTechs = !empty($techIds);

            $ticket->update([
                'service_id'       => $newService->id,
                'ticket_status_id' => 2,
            ]);

            $taskId = DB::table('tickets_task')->where('ticket_id', $ticket->id)->value('id');
            $taskData = [
                'ticket_id'        => $ticket->id,
                'service_id'       => $newService->id,
                'ticket_status_id' => 2,
                'updated_at'       => $now,
            ];

            if (!$taskId) {
                $taskData['created_by']  = Auth::id() ?? 1;
                $taskData['received_at'] = $now;
                $taskData['started_at']  = null; // <-- Menunggu teknisi menekan mulai kerja
                $taskData['created_at']  = $now;
                $taskId = DB::table('tickets_task')->insertGetId($taskData);
            } else {
                $taskData['started_at'] = null;
                DB::table('tickets_task')->where('id', $taskId)->update($taskData);
            }

            DB::table('ticket_task_assignees')->where('ticket_task_id', $taskId)->delete();
            if ($hasTechs) {
                foreach ($techIds as $techId) {
                    DB::table('ticket_task_assignees')->insert([
                        'ticket_task_id' => $taskId,
                        'user_id'        => $techId,
                        'created_at'     => $now,
                        'updated_at'     => $now,
                    ]);
                }
            }

            $techNames = $hasTechs ? User::whereIn('id', $techIds)->pluck('name')->join(', ') : 'Belum ditentukan';
            TicketStatusHistory::create([
                'ticket_id'        => $ticket->id,
                'ticket_task_id'   => $taskId,
                'ticket_status_id' => 2,
                'changed_by'       => Auth::id() ?? 1,
                'note'             => "Triage Selesai: Sub-layanan diatur ke '{$newService->name}', SLA: {$validated['sla_minutes']} menit. Teknisi: {$techNames}.",
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
     * Reassign Tiket ke Teknisi Lain (Internal Bidang - Status Tetap Diproses / ID 2)
     */
    public function reassignTeknisi(Request $request, Tickets $ticket): RedirectResponse
    {
        $techId = $request->technician_id ?? $request->target_bidang_id;

        if (!$techId || !User::where('id', $techId)->exists()) {
            return back()->withErrors(['technician_id' => 'Pilih salah satu teknisi yang valid.']);
        }

        DB::transaction(function () use ($ticket, $techId) {
            $now = now();
            $taskId = DB::table('tickets_task')->where('ticket_id', $ticket->id)->value('id');

            if (!$taskId) {
                $taskId = DB::table('tickets_task')->insertGetId([
                    'ticket_id'        => $ticket->id,
                    'service_id'       => $ticket->service_id ?? 1,
                    'ticket_status_id' => 2,
                    'created_by'       => Auth::id() ?? 1,
                    'received_at'      => $now,
                    'started_at'       => null,
                    'created_at'       => $now,
                    'updated_at'       => $now,
                ]);
            }

            $oldTechId = DB::table('ticket_task_assignees')->where('ticket_task_id', $taskId)->value('user_id');
            $oldTechName = $oldTechId ? (User::find($oldTechId)?->name ?? 'Teknisi Sebelumnya') : 'Belum ada';

            DB::table('ticket_task_assignees')->where('ticket_task_id', $taskId)->delete();

            $assigneeCols = Schema::hasTable('ticket_task_assignees') ? Schema::getColumnListing('ticket_task_assignees') : [];
            $assigneeData = [
                'ticket_task_id' => $taskId,
                'user_id'        => $techId,
            ];
            if (in_array('created_at', $assigneeCols)) $assigneeData['created_at'] = $now;
            if (in_array('updated_at', $assigneeCols)) $assigneeData['updated_at'] = $now;

            DB::table('ticket_task_assignees')->insert($assigneeData);

            $newTechName = User::find($techId)?->name ?? 'Teknisi Baru';

            TicketStatusHistory::create([
                'ticket_id'        => $ticket->id,
                'ticket_task_id'   => $taskId,
                'ticket_status_id' => 2,
                'changed_by'       => Auth::id() ?? 1,
                'note'             => "Reassign Teknisi: Penanganan dialihkan dari {$oldTechName} ke {$newTechName}.",
            ]);

            $ticket->touch();
        });

        return back()->with('success', "Penugasan tiket {$ticket->ticket_number} berhasil dialihkan.");
    }

    /**
     * Forward Tiket ke Bidang Lain (Status Kembali ke 1 / Menunggu Triage di Bidang Tujuan)
     */
    public function forwardTicket(Request $request, Tickets $ticket): RedirectResponse
    {
        $validated = $request->validate([
            'target_bidang_id' => 'required',
            'forward_note'     => 'nullable|string|max:500',
        ]);

        $targetBidang = DB::table('bidang')->where('id', $validated['target_bidang_id'])->first();
        $bidangName = $targetBidang->name ?? 'Bidang Terkait';

        DB::transaction(function () use ($ticket, $validated, $bidangName) {
            $now = now();

            $ticket->update([
                'ticket_status_id' => 1,
            ]);

            $taskId = DB::table('tickets_task')->where('ticket_id', $ticket->id)->value('id');
            if ($taskId) {
                DB::table('ticket_task_assignees')->where('ticket_task_id', $taskId)->delete();
                DB::table('tickets_task')->where('id', $taskId)->update([
                    'ticket_status_id' => 1,
                    'updated_at'       => $now,
                ]);
            }

            $noteText = "Forward Tiket: Aduan diteruskan ke {$bidangName}.";
            if (!empty($validated['forward_note'])) {
                $noteText .= " Alasan/Catatan: {$validated['forward_note']}";
            }

            TicketStatusHistory::create([
                'ticket_id'        => $ticket->id,
                'ticket_task_id'   => $taskId,
                'ticket_status_id' => 1,
                'changed_by'       => Auth::id() ?? 1,
                'note'             => $noteText,
            ]);
        });

        return redirect()->route('bidang.triage')->with('success', "Tiket {$ticket->ticket_number} berhasil diteruskan (Forward) ke {$bidangName}.");
    }

    /**
     * Alias penjamin backward compatibility jika route masih menembak reassignBidang
     */
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
        $sops = collect();
        if (Schema::hasTable('sops')) {
            $sops = DB::table('sops')->get()->map(function ($sop) {
                $steps = [];
                if (Schema::hasTable('sops_steps')) {
                    $steps = DB::table('sops_steps')->where('sop_id', $sop->id)->get();
                }
                $sop->steps = $steps;
                return $sop;
            });
        }

        $categories = DB::table('service_categories')->get()->map(function ($cat) {
            $services = DB::table('services')
                ->where('category_id', $cat->id)
                ->get()
                ->map(function ($svc) {
                    $minutes = $svc->sla_minutes ?? ((isset($svc->sla_hours) ? $svc->sla_hours * 60 : 240));
                    $svc->sla_minutes_val = (int) $minutes;
                    $svc->sla_display = $this->formatMinutes((int) $minutes);

                    $activities = [];
                    if (Schema::hasTable('service_activities')) {
                        $activities = DB::table('service_activities')
                            ->where('service_id', $svc->id)
                            ->get();
                    }
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

        if (Schema::hasTable('service_activities')) {
            $actCols = Schema::getColumnListing('service_activities');
            $actData = ['service_id' => $serviceId, 'name' => $validated['name']];

            if (in_array('targer_minutes', $actCols)) $actData['targer_minutes'] = 0;
            if (in_array('target_minutes', $actCols)) $actData['target_minutes'] = 0;
            if (in_array('duration_minutes', $actCols)) $actData['duration_minutes'] = 0;
            if (in_array('created_at', $actCols)) {
                $actData['created_at'] = now();
                $actData['updated_at'] = now();
            }

            DB::table('service_activities')->insert($actData);
        }

        return redirect()->route('bidang.kategori')->with('success', 'Aktivitas SOP berhasil ditambahkan.');
    }

    public function storeCategory(Request $request): RedirectResponse
    {
        $validated = $request->validate(['name' => 'required|string|max:255']);
        $catCols = Schema::getColumnListing('service_categories');
        $catData = ['name' => $validated['name']];

        if (in_array('code', $catCols)) $catData['code'] = rand(10, 99);
        if (in_array('bidang_id', $catCols)) $catData['bidang_id'] = DB::table('bidang')->value('id') ?? 1;
        if (in_array('perangkat_daerah_id', $catCols)) $catData['perangkat_daerah_id'] = DB::table('perangkat_daerah')->value('id') ?? 1;
        if (in_array('created_at', $catCols)) {
            $catData['created_at'] = now();
            $catData['updated_at'] = now();
        }

        DB::table('service_categories')->insert($catData);
        return redirect()->route('bidang.kategori')->with('success', "Kategori '{$validated['name']}' berhasil ditambahkan.");
    }

    public function storeService(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:service_categories,id',
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'sla_minutes' => 'required|numeric|min:5',
            'work_hours'  => 'nullable|string|max:100',
        ]);

        $svcCols = Schema::getColumnListing('services');
        $svcData = [
            'category_id' => $validated['category_id'],
            'name'        => $validated['name'],
            'description' => $validated['description'] ?? 'Layanan bidang',
        ];

        if (in_array('code', $svcCols)) $svcData['code'] = rand(100, 999);
        if (in_array('sla_minutes', $svcCols)) $svcData['sla_minutes'] = $validated['sla_minutes'];
        if (in_array('sla_hours', $svcCols)) $svcData['sla_hours'] = round($validated['sla_minutes'] / 60, 2);
        if (in_array('work_hours', $svcCols)) $svcData['work_hours'] = $validated['work_hours'] ?? '08:00 - 15:00 WITA';
        if (in_array('bidang_id', $svcCols)) $svcData['bidang_id'] = DB::table('bidang')->value('id') ?? 1;
        if (in_array('perangkat_daerah_id', $svcCols)) $svcData['perangkat_daerah_id'] = DB::table('perangkat_daerah')->value('id') ?? 1;
        if (in_array('created_at', $svcCols)) {
            $svcData['created_at'] = now();
            $svcData['updated_at'] = now();
        }

        DB::table('services')->insert($svcData);
        return redirect()->route('bidang.kategori')->with('success', "Layanan '{$validated['name']}' berhasil ditambahkan.");
    }

    public function updateSla(Request $request, $serviceId): RedirectResponse
    {
        $validated = $request->validate([
            'sla_minutes' => 'required|numeric|min:5',
            'work_hours'  => 'required|string|max:100',
        ]);

        $updateData = [];
        if (Schema::hasColumn('services', 'sla_minutes')) $updateData['sla_minutes'] = $validated['sla_minutes'];
        if (Schema::hasColumn('services', 'sla_hours')) $updateData['sla_hours'] = round($validated['sla_minutes'] / 60, 2);
        if (Schema::hasColumn('services', 'work_hours')) $updateData['work_hours'] = $validated['work_hours'];

        if (!empty($updateData)) {
            DB::table('services')->where('id', $serviceId)->update($updateData);
        }

        return redirect()->route('bidang.kategori')->with('success', 'Target SLA layanan berhasil disesuaikan.');
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

    /**
     * Buka PDF SOP berdasarkan Slug
     */
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

        if (Schema::hasTable('sops')) {
            $uploaded = $request->file('file');
            $disk = 'public';
            $path = $uploaded->store('sops', $disk);
            $fileId = null;

            if (Schema::hasTable('files')) {
                $fileData = [
                    'disk'            => $disk,
                    'path'            => $path,
                    'original_name'   => $uploaded->getClientOriginalName(),
                    'mime_type'       => $uploaded->getClientMimeType(),
                    'size_bytes'      => $uploaded->getSize(),
                    'checksum_sha256' => hash_file('sha256', $uploaded->getRealPath()),
                    'uploaded_by'     => Auth::id() ?? 1,
                    'created_at'      => now(),
                ];

                $fileId = DB::table('files')->insertGetId($fileData);
            }

            $latestVersion = DB::table('sops')->where('service_id', $validated['service_id'])->max('version') ?? 0;
            $version = $latestVersion + 1;

            $baseSlug = Str::slug($validated['name']) . '-v' . $version;
            $slug = $baseSlug;
            $counter = 1;
            while (DB::table('sops')->where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter++;
            }

            $cols = Schema::getColumnListing('sops');
            $data = [];

            if (in_array('name', $cols)) $data['name'] = $validated['name'];
            if (in_array('title', $cols)) $data['title'] = $validated['name'];
            if (in_array('slug', $cols)) $data['slug'] = $slug;
            if (in_array('service_id', $cols)) $data['service_id'] = $validated['service_id'];
            if (in_array('file_id', $cols)) $data['file_id'] = $fileId ?? 0;
            if (in_array('version', $cols)) $data['version'] = $version;
            if (in_array('created_by', $cols)) $data['created_by'] = Auth::id() ?? 1;

            if (in_array('doc_number', $cols)) $data['doc_number'] = $validated['doc_number'];
            if (in_array('description', $cols)) $data['description'] = $validated['description'];
            if (in_array('file_path', $cols)) $data['file_path'] = $path;

            if (in_array('created_at', $cols)) $data['created_at'] = now();
            if (in_array('updated_at', $cols)) $data['updated_at'] = now();

            DB::table('sops')->insert($data);
        }

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

        if (Schema::hasTable('sops')) {
            $cols = Schema::getColumnListing('sops');
            $data = [];

            if (in_array('name', $cols)) $data['name'] = $validated['name'];
            if (in_array('title', $cols)) $data['title'] = $validated['name'];
            if (in_array('service_id', $cols) && !empty($validated['service_id'])) {
                $data['service_id'] = $validated['service_id'];
            }
            if (in_array('doc_number', $cols)) $data['doc_number'] = $validated['doc_number'];
            if (in_array('description', $cols)) $data['description'] = $validated['description'];

            if ($request->hasFile('file')) {
                $uploaded = $request->file('file');
                $disk = 'public';
                $path = $uploaded->store('sops', $disk);

                if (Schema::hasTable('files')) {
                    $fileData = [
                        'disk'            => $disk,
                        'path'            => $path,
                        'original_name'   => $uploaded->getClientOriginalName(),
                        'mime_type'       => $uploaded->getClientMimeType(),
                        'size_bytes'      => $uploaded->getSize(),
                        'checksum_sha256' => hash_file('sha256', $uploaded->getRealPath()),
                        'uploaded_by'     => Auth::id() ?? 1,
                        'created_at'      => now(),
                    ];

                    $newFileId = DB::table('files')->insertGetId($fileData);
                    if (in_array('file_id', $cols)) {
                        $data['file_id'] = $newFileId;
                    }
                }

                if (in_array('version', $cols)) {
                    $currentVersion = DB::table('sops')->where('id', $id)->value('version') ?? 1;
                    $newVersion = $currentVersion + 1;
                    $data['version'] = $newVersion;

                    if (in_array('slug', $cols)) {
                        $data['slug'] = Str::slug($validated['name']) . '-v' . $newVersion;
                    }
                }
            }

            if (in_array('updated_at', $cols)) $data['updated_at'] = now();

            DB::table('sops')->where('id', $id)->update($data);
        }

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
}
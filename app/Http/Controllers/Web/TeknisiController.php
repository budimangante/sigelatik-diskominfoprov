<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Files;
use App\Models\Tickets;
use App\Models\TicketStatuses;
use App\Models\TicketStatusHistory;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class TeknisiController extends Controller
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

    /**
     * Ambil ID status tiket secara dinamis sesuai database
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
            'selesai', 'closed', 'resolved', 'solved' => TicketStatuses::where('code', 'selesai')
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

        $newStatus = TicketStatuses::create([
            'name'       => ucfirst($type),
            'code'       => strtolower($type),
            'sort_order' => 1,
        ]);

        return (int) $newStatus->id;
    }

    /**
     * Halaman Daftar Tugas Tiket Teknisi
     */
    public function index(Request $request): View
    {
        $userId = Auth::id();

        // 1. Ambil ID tiket yang ditugaskan ke teknisi login (Cari via relasi tabel pivot)
        $assignedTicketIds = DB::table('ticket_task_assignees')
            ->join('tickets_task', 'tickets_task.id', '=', 'ticket_task_assignees.ticket_task_id')
            ->where('ticket_task_assignees.user_id', $userId)
            ->pluck('tickets_task.ticket_id')
            ->unique()
            ->toArray();

        // 2. Ambil ID status selesai/tutup agar tidak mematikan tiket yang ber-ID dinamis
        $closedStatusIds = TicketStatuses::where(function ($q) {
            $q->where('code', 'like', '%selesai%')
              ->orWhere('code', 'like', '%closed%')
              ->orWhere('code', 'like', '%solved%')
              ->orWhere('code', 'like', '%batal%')
              ->orWhere('name', 'like', '%selesai%')
              ->orWhere('name', 'like', '%tutup%')
              ->orWhere('name', 'like', '%batal%');
        })->pluck('id')->toArray();

        // 3. Query Tiket Aktif Teknisi
        $query = Tickets::with(['user', 'status', 'service', 'perangkatDaerah'])
            ->where(function ($q) use ($assignedTicketIds, $userId) {
                $q->whereIn('id', $assignedTicketIds)
                  ->orWhereHas('tasks.assignees', fn($sq) => $sq->where('users.id', $userId));
            })
            ->whereNotIn('ticket_status_id', $closedStatusIds)
            ->whereNull('solved_at')
            ->whereNull('cancelled_at');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status_id')) {
            $query->where('ticket_status_id', $request->status_id);
        }

        $now = now();
        $pendingId = $this->getStatusId('pending');

        $tickets = $query->withCount(['messages as unread_messages_count' => function ($q) use ($userId) {
                $q->where('user_id', '!=', $userId)
                  ->whereNull('read_at');
            }])
            ->latest('updated_at')
            ->get()
            ->map(function ($ticket) use ($now, $pendingId) {
                $task = DB::table('tickets_task')->where('ticket_id', $ticket->id)->first();

                $taskMinutes = 0;
                if ($task) {
                    $taskMinutes = (int) DB::table('ticket_task_activities')
                        ->where('ticket_task_id', $task->id)
                        ->sum('target_minutes_snapshot');
                }

                $targetSla = $taskMinutes > 0 
                    ? $taskMinutes 
                    : (int) ($ticket->service?->sla_minutes ?? 240);

                $ticket->target_sla_minutes = $targetSla;
                $ticket->target_sla_label = $this->formatMinutes($targetSla);
                $ticket->is_started = !empty($task?->started_at);
                $ticket->task_started_at = $task?->started_at;

                // Hitung SLA Penanganan Teknisi
                if (!$ticket->is_started) {
                    $ticket->sla_progress = 0;
                    $ticket->is_overdue = false;
                    $ticket->sla_status = 'not_started';
                } elseif ($ticket->ticket_status_id == $pendingId) {
                    $freezeTime = $ticket->updated_at ? Carbon::parse($ticket->updated_at) : $now;
                    $elapsed = max(0, (int) Carbon::parse($task->started_at)->diffInMinutes($freezeTime));
                    $ticket->sla_progress = min(100, (int) round(($elapsed / max(1, $targetSla)) * 100));
                    $ticket->is_overdue = $elapsed >= $targetSla;
                    $ticket->sla_status = 'paused';
                } else {
                    $elapsed = max(0, (int) Carbon::parse($task->started_at)->diffInMinutes($now));
                    $ticket->sla_progress = min(100, (int) round(($elapsed / max(1, $targetSla)) * 100));
                    $ticket->is_overdue = $elapsed >= $targetSla;
                    $ticket->sla_status = $ticket->is_overdue ? 'overdue' : 'running';
                }

                return $ticket;
            });

        // Ambil status aktif secara dinamis untuk filter view
        $statuses = TicketStatuses::whereNotIn('id', $closedStatusIds)
            ->orderBy('sort_order', 'asc')
            ->get();

        return view('teknisi.index', compact('tickets', 'statuses'));
    }

    /**
     * Halaman Workspace Detail Pengerjaan Tiket & Form Penanganan
     */
    public function show(Tickets $ticket): View
    {
        $ticket->loadMissing([
            'user', 
            'status', 
            'service', 
            'perangkatDaerah',
            'statusHistories.user',
            'messages.user'
        ]);

        $task = DB::table('tickets_task')->where('ticket_id', $ticket->id)->first();

        $taskMinutes = 0;
        if ($task) {
            $taskMinutes = (int) DB::table('ticket_task_activities')
                ->where('ticket_task_id', $task->id)
                ->sum('target_minutes_snapshot');
        }

        $targetSla = $taskMinutes > 0 
            ? $taskMinutes 
            : (int) ($ticket->service?->sla_minutes ?? 240);

        $slaHours = round($targetSla / 60, 1);
        $targetLabel = $this->formatMinutes($targetSla);
        $isStarted = !empty($task?->started_at);

        $statusProsesId  = $this->getStatusId('diproses');
        $statusPendingId = $this->getStatusId('pending');
        $statusSelesaiId = $this->getStatusId('selesai');

        $currentStatusId = (int) $ticket->ticket_status_id;
        $isPaused = ($currentStatusId === $statusPendingId);

        $dueAt = null;
        $remainingSeconds = 0;
        $isOverdue = false;

        if ($isStarted) {
            $startTime = Carbon::parse($task->started_at);
            $dueAt = $startTime->copy()->addMinutes($targetSla);

            if ($isPaused) {
                $freezeTime = $ticket->updated_at ? Carbon::parse($ticket->updated_at) : now();
                $elapsed = (int) $startTime->diffInMinutes($freezeTime);
                $remainingSeconds = max(0, ($targetSla - $elapsed) * 60);
                $isOverdue = $elapsed >= $targetSla;
            } else {
                $diffSecs = (int) now()->diffInSeconds($dueAt, false);
                $isOverdue = $diffSecs <= 0;
                $remainingSeconds = max(0, $diffSecs);
            }
        }

        return view('teknisi.show', compact(
            'ticket',
            'task',
            'isStarted',
            'isPaused',
            'targetSla',
            'slaHours',         
            'targetLabel',
            'dueAt',
            'remainingSeconds',
            'isOverdue',
            'statusProsesId',
            'statusPendingId',
            'statusSelesaiId'
        ));
    }

    /**
     * Aksi Tombol: Mulai Kerjakan Tiket
     */
    public function startWork(Tickets $ticket): RedirectResponse
    {
        $statusProsesId = $this->getStatusId('diproses');

        DB::transaction(function () use ($ticket, $statusProsesId) {
            $now = now();
            $taskId = DB::table('tickets_task')->where('ticket_id', $ticket->id)->value('id');

            if ($taskId) {
                DB::table('tickets_task')->where('id', $taskId)->update([
                    'started_at'       => $now,
                    'ticket_status_id' => $statusProsesId,
                    'updated_at'       => $now,
                ]);
            }

            $ticket->update([
                'ticket_status_id' => $statusProsesId,
            ]);

            TicketStatusHistory::create([
                'ticket_id'        => $ticket->id,
                'ticket_task_id'   => $taskId,
                'ticket_status_id' => $statusProsesId,
                'changed_by'       => Auth::id() ?? 1,
                'note'             => 'Teknisi mengonfirmasi penugasan dan mulai menangani tiket.',
            ]);
        });

        return back()->with('success', 'Penanganan tiket dimulai. Timer SLA penanganan sedang berjalan.');
    }

    /**
     * Simpan Laporan & Update Status Tiket
     */
    public function updateProgress(Request $request, Tickets $ticket): JsonResponse
    {
        $request->validate([
            'ticket_status_id'   => 'required|exists:ticket_statuses,id',
            'notes'              => 'nullable|string|max:5000',
            'pending_reason'     => 'nullable|string|max:255',
            'teruskan_ke_bidang' => 'nullable|boolean',
            'document'           => 'nullable|file|mimes:pdf,jpg,png,docx|max:10240',
        ]);

        $statusSelesaiId = $this->getStatusId('selesai');
        $statusPendingId = $this->getStatusId('pending');

        $isForwarded = $request->boolean('teruskan_ke_bidang');
        $statusId = $isForwarded ? $statusSelesaiId : (int) $request->ticket_status_id;

        $isFinal = ($statusId === $statusSelesaiId);
        $isPending = ($statusId === $statusPendingId);

        // 1. Simpan dokumen lampiran jika ada
        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $path = $file->store('tickets/reports', 'public');

            $fileRecord = Files::create([
                'disk'            => 'public',
                'path'            => $path,
                'original_name'   => $file->getClientOriginalName(),
                'mime_type'       => $file->getClientMimeType(),
                'size_bytes'      => $file->getSize(),
                'checksum_sha256' => hash_file('sha256', $file->getRealPath()),
                'uploaded_by'     => Auth::id() ?? 1,
            ]);

            $ticket->ticketFiles()->attach($fileRecord->id);
        }

        // 2. Timestamp Tiket
        if ($isFinal) {
            $ticket->solved_at = now();
            $ticket->cancelled_at = null;
        } else {
            $ticket->solved_at = null;
            $ticket->cancelled_at = null;
        }

        $ticket->ticket_status_id = $statusId;
        $ticket->save();

        // 3. Update Status Task
        if (Schema::hasTable('tickets_task')) {
            $task = DB::table('tickets_task')->where('ticket_id', $ticket->id)->first();
            if ($task) {
                $taskUpdate = [
                    'ticket_status_id' => $statusId,
                    'updated_at'       => now(),
                ];

                if ($isFinal && Schema::hasColumn('tickets_task', 'completed_at')) {
                    $taskUpdate['completed_at'] = now();
                }

                DB::table('tickets_task')->where('id', $task->id)->update($taskUpdate);
            }
        }

        // 4. Catat Riwayat
        try {
            if (Schema::hasTable('ticket_status_histories')) {
                $historiesColumns = Schema::getColumnListing('ticket_status_histories');
                $userNotes = trim($request->notes ?? '');

                if ($isForwarded) {
                    $noteText = 'Tiket diteruskan ke Helpdesk Bidang dan diselesaikan.' . ($userNotes !== '' ? ' Catatan: ' . $userNotes : '');
                } elseif ($isPending) {
                    $reason = $request->pending_reason ?? 'Di luar jam operasional / Dilanjutkan besok';
                    $noteText = "[PAUSED_TEKNISI] Pengerjaan dijeda. Alasan: {$reason}." . ($userNotes !== '' ? ' Catatan: ' . $userNotes : '');
                } elseif ($isFinal) {
                    $noteText = 'Penanganan tiket telah selesai.' . ($userNotes !== '' ? ' Catatan: ' . $userNotes : '');
                } else {
                    $noteText = $userNotes !== '' ? $userNotes : 'Pembaruan tindakan teknisi.';
                }

                $ticketTaskId = DB::table('tickets_task')->where('ticket_id', $ticket->id)->value('id');

                $historyData = [
                    'ticket_id'        => $ticket->id,
                    'ticket_status_id' => $statusId,
                ];

                if (in_array('ticket_task_id', $historiesColumns) && $ticketTaskId) $historyData['ticket_task_id'] = $ticketTaskId;
                if (in_array('changed_by', $historiesColumns)) $historyData['changed_by'] = Auth::id() ?? 1;
                if (in_array('note', $historiesColumns)) $historyData['note'] = $noteText;
                if (in_array('created_at', $historiesColumns)) $historyData['created_at'] = now();
                if (in_array('updated_at', $historiesColumns)) $historyData['updated_at'] = now();

                DB::table('ticket_status_histories')->insert($historyData);
            }
        } catch (\Throwable $e) {
            Log::warning('Riwayat status tiket gagal disimpan: ' . $e->getMessage());
        }

        $ticket->load('status');
        $statusName = $ticket->status->name ?? 'Status Diperbarui';

        $title = $isFinal ? 'Tiket Telah Selesai' : ($isPending ? 'Tiket Dijeda (Pending)' : 'Laporan Disimpan');
        $message = $isFinal 
            ? "Tiket telah berstatus {$statusName} dan dialihkan ke Riwayat Tiket."
            : ($isPending 
                ? "SLA penanganan dibekukan sementara. Anda dapat melanjutkannya esok hari." 
                : "Status tiket berhasil diubah menjadi {$statusName}.");

        return response()->json([
            'success'      => true,
            'is_solved'    => $isFinal,
            'title'        => $title,
            'message'      => $message,
            'status_id'    => $statusId,
            'status_name'  => $statusName,
            'redirect_url' => route('teknisi.history'),
        ]);
    }

    /**
     * Halaman Riwayat Tiket (History)
     */
    public function historyIndex(Request $request): View
    {
        $userId = Auth::id();

        $assignedTicketIds = DB::table('ticket_task_assignees')
            ->join('tickets_task', 'tickets_task.id', '=', 'ticket_task_assignees.ticket_task_id')
            ->where('ticket_task_assignees.user_id', $userId)
            ->pluck('tickets_task.ticket_id')
            ->toArray();

        $statusSelesaiId = $this->getStatusId('selesai');

        $query = Tickets::with(['user', 'status', 'service', 'perangkatDaerah'])
            ->where(function ($q) use ($assignedTicketIds, $userId) {
                $q->whereIn('id', $assignedTicketIds)
                  ->orWhereHas('tasks.assignees', fn($sq) => $sq->where('users.id', $userId));
            })
            ->where(function ($q) use ($statusSelesaiId) {
                $q->where('ticket_status_id', $statusSelesaiId)
                  ->orWhereNotNull('solved_at')
                  ->orWhereNotNull('cancelled_at');
            });

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status_id')) {
            $query->where('ticket_status_id', $request->status_id);
        }

        $tickets = $query->latest('updated_at')->get();

        return view('teknisi.history-teknisi', compact('tickets'));
    }

    /**
     * Detail Riwayat Tiket
     */
    public function historyShow(Tickets $ticket): View
    {
        $ticket->loadMissing([
            'user', 
            'status', 
            'service', 
            'perangkatDaerah',
            'messages.user', 
            'statusHistories' => function ($q) {
                $q->reorder('id', 'asc');
            },
            'statusHistories.user'
        ]);

        $task = DB::table('tickets_task')->where('ticket_id', $ticket->id)->first();

        $taskMinutes = 0;
        if ($task) {
            $taskMinutes = (int) DB::table('ticket_task_activities')
                ->where('ticket_task_id', $task->id)
                ->sum('target_minutes_snapshot');
        }

        $totalDurationMinutes = $taskMinutes > 0 
            ? $taskMinutes 
            : (int) ($ticket->service?->sla_minutes ?? 240);

        $slaHours = round($totalDurationMinutes / 60, 1);

        $startTime = $task?->started_at ? Carbon::parse($task->started_at) : ($ticket->created_at ? Carbon::parse($ticket->created_at) : now());
        $finishTime = $ticket->solved_at 
            ? Carbon::parse($ticket->solved_at) 
            : ($ticket->cancelled_at ? Carbon::parse($ticket->cancelled_at) : now());

        $elapsedMinutes = max(0, $startTime->diffInMinutes($finishTime));
        $slaProgressPercent = min(100, round(($elapsedMinutes / max(1, $totalDurationMinutes)) * 100));
        $isSlaOverdue = $elapsedMinutes >= $totalDurationMinutes;

        return view('teknisi.history-teknisi-show', compact(
            'ticket', 
            'slaHours', 
            'slaProgressPercent', 
            'isSlaOverdue',
            'startTime',
            'finishTime'
        ));
    }
}
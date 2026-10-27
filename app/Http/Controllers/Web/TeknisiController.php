<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Tickets;
use App\Models\Files;
use App\Models\TicketStatusHistory;
use App\Models\TicketStatuses;
use Carbon\Carbon;
use App\Events\TicketStatusUpdated;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class TeknisiController extends Controller
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
     * Halaman Daftar Tugas Tiket Teknisi
     * Hanya menampilkan tiket berstatus aktif (1, 2, 3) yang DITUGASKAN ke teknisi login
     */
    public function index(Request $request): View
    {
        $userId = Auth::id();

        // 1. Ambil ID tiket yang ditugaskan khusus ke teknisi yang sedang login
        $assignedTicketIds = DB::table('ticket_task_assignees')
            ->join('tickets_task', 'tickets_task.id', '=', 'ticket_task_assignees.ticket_task_id')
            ->where('ticket_task_assignees.user_id', $userId)
            ->pluck('tickets_task.ticket_id')
            ->toArray();

        // 2. Filter tiket hanya yang menjadi tanggung jawab teknisi ini
        $query = Tickets::with(['user', 'status', 'service'])
            ->whereIn('id', $assignedTicketIds)
            ->whereIn('ticket_status_id', [1, 2, 3]);

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
        $tickets = $query->withCount(['messages as unread_messages_count' => function ($q) use ($userId) {
                $q->where('user_id', '!=', $userId)
                  ->whereNull('read_at');
            }])
            ->latest('updated_at')
            ->get()
            ->map(function ($ticket) use ($now) {
                $task = DB::table('tickets_task')->where('ticket_id', $ticket->id)->first();
                $targetSla = (int) ($ticket->service?->sla_minutes ?? 240);
                $ticket->target_sla_label = $this->formatMinutes($targetSla);

                // Perhitungan SLA Penanganan Teknisi:
                if (!$task || empty($task->started_at)) {
                    // Belum dimulai teknisi
                    $ticket->sla_progress = 0;
                    $ticket->is_overdue = false;
                    $ticket->sla_status = 'not_started';
                } elseif ($ticket->ticket_status_id == 3) {
                    // Sedang pending/dijeda -> freeze timer
                    $freezeTime = $ticket->updated_at ? Carbon::parse($ticket->updated_at) : $now;
                    $elapsed = max(0, (int) Carbon::parse($task->started_at)->diffInMinutes($freezeTime));
                    $ticket->sla_progress = min(100, (int) round(($elapsed / max(1, $targetSla)) * 100));
                    $ticket->is_overdue = $elapsed >= $targetSla;
                    $ticket->sla_status = 'paused';
                } else {
                    // Sedang berjalan (Status 2)
                    $elapsed = max(0, (int) Carbon::parse($task->started_at)->diffInMinutes($now));
                    $ticket->sla_progress = min(100, (int) round(($elapsed / max(1, $targetSla)) * 100));
                    $ticket->is_overdue = $elapsed >= $targetSla;
                    $ticket->sla_status = $ticket->is_overdue ? 'overdue' : 'running';
                }

                return $ticket;
            });

        $statuses = TicketStatuses::whereIn('id', [1, 2, 3])->orderBy('sort_order')->get();

        return view('teknisi.index', compact('tickets', 'statuses'));
    }

    /**
     * Halaman Workspace Detail Pengerjaan Tiket & Form Penanganan
     */
    public function show(Tickets $ticket): View
    {
        $ticket->loadMissing(['user', 'status', 'service']);

        $statuses = TicketStatuses::orderBy('sort_order', 'asc')->get();

        return view('teknisi.show', compact('ticket', 'statuses'));
    }

    /**
     * Simpan Laporan & Update Status Tiket
     * Memulai timer SLA saat teknisi mengubah status ke 'Proses' (Status 2)
     */
    public function updateProgress(Request $request, Tickets $ticket): JsonResponse
    {
        $request->validate([
            'ticket_status_id'   => 'nullable|required_without:teruskan_ke_bidang|exists:ticket_statuses,id',
            'notes'              => 'nullable|string|max:5000',
            'teruskan_ke_bidang' => 'nullable|boolean',
            'document'           => 'nullable|file|mimes:pdf,jpg,png,docx|max:10240',
        ]);

        $isForwarded = $request->boolean('teruskan_ke_bidang');

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

        // 2. Tentukan status
        if ($isForwarded) {
            $statusId = 4; // Otomatis Solved jika diteruskan ke bidang
        } else {
            $statusId = (int) $request->ticket_status_id;
        }

        $isFinal = in_array($statusId, [4, 5]);

        if ($statusId === 4) {
            $ticket->solved_at = now();
            $ticket->cancelled_at = null;
        } elseif ($statusId === 5) {
            $ticket->cancelled_at = now();
            $ticket->solved_at = null;
        } else {
            $ticket->solved_at = null;
            $ticket->cancelled_at = null;
        }

        $ticket->ticket_status_id = $statusId;
        $ticket->save();

        // 3. Update tickets_task & Trigger Timer SLA
        if (Schema::hasTable('tickets_task')) {
            $task = DB::table('tickets_task')->where('ticket_id', $ticket->id)->first();
            if ($task) {
                $taskCols = Schema::getColumnListing('tickets_task');
                $taskUpdate = [];

                if (in_array('ticket_status_id', $taskCols)) {
                    $taskUpdate['ticket_status_id'] = $statusId;
                }
                if (in_array('updated_at', $taskCols)) {
                    $taskUpdate['updated_at'] = now();
                }

                // AKTIFKAN TIMER SLA: Jika status berubah ke 2 (Proses) dan started_at masih null
                if ($statusId === 2 && empty($task->started_at) && in_array('started_at', $taskCols)) {
                    $taskUpdate['started_at'] = now();
                }

                if ($isFinal && in_array('completed_at', $taskCols)) {
                    $taskUpdate['completed_at'] = now();
                }

                if (!empty($taskUpdate)) {
                    DB::table('tickets_task')->where('id', $task->id)->update($taskUpdate);
                }
            }
        }

        // 4. Catat riwayat penanganan ke ticket_status_histories
        try {
            if (Schema::hasTable('ticket_status_histories')) {
                $historiesColumns = Schema::getColumnListing('ticket_status_histories');

                if ($isForwarded) {
                    $userNotes = trim($request->notes ?? '');
                    $noteText = 'Tiket diteruskan ke Helpdesk Bidang dan diselesaikan.' . ($userNotes !== '' ? ' Catatan: ' . $userNotes : '');
                } else {
                    $noteText = $request->filled('notes') ? $request->notes : 'Pembaruan tindakan teknisi.';
                }

                $ticketTaskId = null;
                if (Schema::hasTable('tickets_task')) {
                    $ticketTaskId = DB::table('tickets_task')->where('ticket_id', $ticket->id)->value('id');
                }

                $pendingReasonId = $request->pending_reasons_id ?? null;
                if (!$pendingReasonId && Schema::hasTable('pending_reasons')) {
                    $pendingReasonId = DB::table('pending_reasons')->value('id');
                }

                $historyData = [
                    'ticket_id'        => $ticket->id,
                    'ticket_status_id' => $statusId,
                ];

                if (in_array('ticket_task_id', $historiesColumns) && $ticketTaskId) {
                    $historyData['ticket_task_id'] = $ticketTaskId;
                }

                if (in_array('pending_reasons_id', $historiesColumns) && $pendingReasonId) {
                    $historyData['pending_reasons_id'] = $pendingReasonId;
                }

                if (in_array('changed_by', $historiesColumns)) {
                    $historyData['changed_by'] = Auth::id() ?? 1;
                } elseif (in_array('user_id', $historiesColumns)) {
                    $historyData['user_id'] = Auth::id() ?? 1;
                }

                if (in_array('note', $historiesColumns)) {
                    $historyData['note'] = $noteText;
                } elseif (in_array('notes', $historiesColumns)) {
                    $historyData['notes'] = $noteText;
                }

                if (in_array('created_at', $historiesColumns)) {
                    $historyData['created_at'] = now();
                }
                if (in_array('updated_at', $historiesColumns)) {
                    $historyData['updated_at'] = now();
                }

                DB::table('ticket_status_histories')->insert($historyData);
            }
        } catch (\Throwable $e) {
            Log::warning('Riwayat status tiket gagal disimpan: ' . $e->getMessage());
        }

        $ticket->load('status');
        $statusName = $ticket->status->name ?? 'Status Diperbarui';

        if (class_exists(TicketStatusUpdated::class)) {
            broadcast(new TicketStatusUpdated($ticket, $statusName))->toOthers();
        }

        return response()->json([
            'success'      => true,
            'is_solved'    => $isFinal,
            'title'        => $isFinal ? 'Tiket Telah Selesai' : 'Laporan Disimpan',
            'message'      => $isFinal 
                ? ($isForwarded 
                    ? "Tiket telah diteruskan ke Helpdesk Bidang dan dialihkan ke Riwayat Tiket." 
                    : "Tiket telah berstatus {$statusName} dan dialihkan ke Riwayat Tiket.")
                : "Status tiket berhasil diubah menjadi {$statusName}.",
            'status_id'    => $statusId,
            'status_name'  => $statusName,
            'redirect_url' => route('teknisi.history'),
        ]);
    }

    /**
     * Halaman Riwayat Tiket (History)
     * Hanya menampilkan tiket milik teknisi login yang berstatus tuntas: 4 (Solved) & 5 (Canceled)
     */
    public function historyIndex(Request $request): View
    {
        $userId = Auth::id();

        $assignedTicketIds = DB::table('ticket_task_assignees')
            ->join('tickets_task', 'tickets_task.id', '=', 'ticket_task_assignees.ticket_task_id')
            ->where('ticket_task_assignees.user_id', $userId)
            ->pluck('tickets_task.ticket_id')
            ->toArray();

        $query = Tickets::with(['user', 'status', 'service'])
            ->whereIn('id', $assignedTicketIds)
            ->whereIn('ticket_status_id', [4, 5]);

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
     * Detail Riwayat Tiket 4-Tab
     */
    public function historyShow(Tickets $ticket): View
    {
        $ticket->loadMissing([
            'user', 
            'status', 
            'service', 
            'messages.user', 
            'statusHistories' => function ($q) {
                $q->reorder('id', 'asc');
            },
            'statusHistories.user'
        ]);

        $slaHours = 4;
        $createdAt = $ticket->created_at ? Carbon::parse($ticket->created_at) : now();
        $targetTime = $createdAt->copy()->addHours($slaHours);
        $finishTime = $ticket->solved_at 
            ? Carbon::parse($ticket->solved_at) 
            : ($ticket->cancelled_at ? Carbon::parse($ticket->cancelled_at) : now());

        $totalDurationMinutes = $slaHours * 60;
        $elapsedMinutes = max(0, $createdAt->diffInMinutes($finishTime));
        $slaProgressPercent = min(100, round(($elapsedMinutes / $totalDurationMinutes) * 100));
        $isSlaOverdue = $finishTime->greaterThan($targetTime);

        return view('teknisi.history-teknisi-show', compact(
            'ticket', 
            'slaHours', 
            'slaProgressPercent', 
            'isSlaOverdue',
            'createdAt',
            'finishTime'
        ));
    }
}
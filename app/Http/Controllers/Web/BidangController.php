<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Tickets;
use App\Models\User;
use App\Models\TicketTask;
use App\Models\TicketStatusHistory;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BidangController extends Controller
{
    // Helper pemformat menit ke label teks
    /**
     * Helper format menit ke teks (cth: 60 menit -> 1 jam)
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
     * Halaman Utama Tiket Bidang
     */
    /**
     * Halaman Utama Tiket Bidang
     */
    public function index(): View
    {
        $tickets = Tickets::with(['user', 'status', 'service'])
            ->whereIn('ticket_status_id', [1, 2, 3])
            ->latest('updated_at')
            ->get()
            ->map(function ($ticket) {
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

                    // Ambil total menit dari snapshot aktivitas tiket ini
                    $taskMinutes = (int) DB::table('ticket_task_activities')
                        ->where('ticket_task_id', $task->id)
                        ->sum('target_minutes_snapshot');
                }

                $ticket->assigned_technicians = $techNames;
                $ticket->assigned_tech_ids = $assignedTechIds;
                $ticket->task_started_at = $task?->started_at;

                // Jika sudah pernah disimpan (walau belum ada teknisi), pakai taskMinutes.
                // Jika benar-benar belum diapa-apakan, baru ambil default dari master layanan.
                $finalMinutes = $taskMinutes > 0 
                    ? $taskMinutes 
                    : (int) ($ticket->service?->sla_minutes ?? 240);

                $ticket->target_sla_minutes = $finalMinutes;
                $ticket->target_sla_label = $this->formatMinutes($finalMinutes);

                return $ticket;
            });

        $technicians = User::whereHas('roles', function ($q) {
                $q->where('name', 'like', '%teknisi%');
            })
            ->get();

        return view('bidang.index', compact('tickets', 'technicians'));
    }

    /**
     * Simpan Perubahan SLA dan/atau Penugasan Teknisi
     */
    /**
     * Simpan Perubahan SLA dan/atau Penugasan Teknisi
     * Status tiket TIDAK diubah ke PROCESS (menunggu teknisi memproses tiket)
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

            // 1. Buat / Ambil Task (Status tetap mempertahankan status tiket saat ini, misal: NEW)
            $taskId = DB::table('tickets_task')->where('ticket_id', $ticket->id)->value('id');

            $taskData = [
                'ticket_id'        => $ticket->id,
                'service_id'       => $serviceId,
                'ticket_status_id' => $ticket->ticket_status_id, // Tetap ikuti status tiket (tidak dipaksa ke 2/PROCESS)
                'updated_at'       => $now,
            ];

            if (!$taskId) {
                $taskData['created_by']  = Auth::id();
                $taskData['received_at'] = $now;
                $taskData['started_at']  = null; // Timer SLA belum berjalan sampai teknisi mulai kerja
                $taskData['created_at']  = $now;
                $taskId = DB::table('tickets_task')->insertGetId($taskData);
            } else {
                DB::table('tickets_task')->where('id', $taskId)->update($taskData);
            }

            // 2. Hubungkan teknisi terpilih ke ticket_task_assignees
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

            // 3. Simpan snapshot tahapan SOP ke ticket_task_activities
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
                        'start_at'                => null, // Diisi nanti saat teknisi mulai aktivitas
                        'due_at'                  => null, // Dihitung saat aktivitas aktif
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

            // 4. Catat riwayat status (Tiket TIDAK di-update statusnya)
            if ($hasTechs) {
                $techNames = User::whereIn('id', $techIds)->pluck('name')->join(', ');
                TicketStatusHistory::create([
                    'ticket_id'        => $ticket->id,
                    'ticket_task_id'   => $taskId,
                    'ticket_status_id' => $ticket->ticket_status_id,
                    'changed_by'       => Auth::id(),
                    'note'             => "Tiket ditugaskan ke teknisi: {$techNames}. Menunggu teknisi menerima tiket.",
                ]);
            } else {
                TicketStatusHistory::create([
                    'ticket_id'        => $ticket->id,
                    'ticket_task_id'   => $taskId,
                    'ticket_status_id' => $ticket->ticket_status_id,
                    'changed_by'       => Auth::id(),
                    'note'             => "Target SLA tiket diatur ke {$inputMinutes} menit (Belum ditugaskan).",
                ]);
            }
        });

        $pesan = $hasTechs 
            ? 'Teknisi berhasil ditugaskan. Status tiket tetap menunggu konfirmasi dari teknisi.'
            : 'Target SLA tiket berhasil diperbarui.';

        return redirect()->route('bidang.index')->with('success', $pesan);
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

                    // Ambil daftar aktivitas (murni nama langkah SOP)
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

    /**
     * Menambahkan Aktivitas Layanan (Tanpa input SLA)
     */
    public function storeActivity(Request $request, $serviceId): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        if (Schema::hasTable('service_activities')) {
            $actCols = Schema::getColumnListing('service_activities');
            $actData = [
                'service_id' => $serviceId,
                'name'       => $validated['name'],
            ];

            // Isi nilai 0 ke kolom target jika di database berstatus NOT NULL
            if (in_array('targer_minutes', $actCols)) {
                $actData['targer_minutes'] = 0;
            }
            if (in_array('target_minutes', $actCols)) {
                $actData['target_minutes'] = 0;
            }
            if (in_array('duration_minutes', $actCols)) {
                $actData['duration_minutes'] = 0;
            }
            if (in_array('created_at', $actCols)) {
                $actData['created_at'] = now();
                $actData['updated_at'] = now();
            }

            DB::table('service_activities')->insert($actData);
        }

        return redirect()->route('bidang.kategori')
            ->with('success', 'Aktivitas tahapan penanganan SOP berhasil ditambahkan.');
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
}
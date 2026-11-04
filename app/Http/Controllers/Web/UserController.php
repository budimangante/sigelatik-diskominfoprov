<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Bidang;
use App\Models\Files;
use App\Models\PerangkatDaerah;
use App\Models\ServiceCategory;
use App\Models\Services;
use App\Models\TicketFile;
use App\Models\TicketMessage;
use App\Models\Tickets;
use App\Models\TicketStatuses;
use App\Models\TicketStatusHistory;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Formulir Buat Tiket Pelapor
     */
    public function createTicket(): View
    {
        $categories = class_exists(ServiceCategory::class)
            ? ServiceCategory::orderBy('name', 'asc')->get()
            : Services::orderBy('name', 'asc')->get();

        $perangkatList = PerangkatDaerah::orderBy('name', 'asc')->get();

        $dinasList = $perangkatList->isNotEmpty()
            ? $perangkatList->pluck('name')->toArray()
            : [
                'Dinas Komunikasi dan Informatika',
                'Dinas Pendidikan dan Kebudayaan',
                'Dinas Kesehatan',
                'Badan Pengelola Keuangan dan Aset Daerah',
                'Badan Kepegawaian Daerah',
                'Sekretariat Daerah',
                'Inspektorat Daerah',
                'Dinas Perhubungan',
                'Dinas Lingkungan Hidup',
            ];

        return view('user.index', compact('categories', 'dinasList'));
    }

    /**
     * Simpan Pengajuan Tiket Pelapor
     */
    public function storeTicket(Request $request): RedirectResponse
    {
        $request->validate([
            'dinas'       => 'required|string|max:255',
            'category_id' => 'required',
            'subject'     => 'nullable|string|max:255',
            'description' => 'required|string|max:5000',
            'attachment'  => 'nullable|file|mimes:pdf,jpg,jpeg,png,docx|max:10240',
        ], [
            'dinas.required'       => 'Pilih instansi / dinas pelapor.',
            'category_id.required' => 'Pilih kategori layanan yang diinginkan.',
            'description.required' => 'Penjelasan kendala atau kebutuhan wajib diisi.',
            'attachment.max'       => 'Ukuran lampiran maksimal 10 MB.',
        ]);

        $userId = Auth::id();

        // 1. Nomor Tiket
        $today = now()->format('Ymd');
        $countToday = Tickets::whereDate('created_at', today())->count() + 1;
        $ticketNumber = 'TCK-' . $today . '-' . str_pad($countToday, 3, '0', STR_PAD_LEFT);

        $subject = trim($request->subject ?? '') !== '' 
            ? $request->subject 
            : Str::limit(strip_tags($request->description), 60);

        $fullDescription = "[Instansi: {$request->dinas}]\n" . $request->description;

        // 2. Hubungkan Perangkat Daerah Pemohon & Bidang Penanganan Diskominfo
        $dinasInput = trim($request->dinas);
        $opd = PerangkatDaerah::where('name', $dinasInput)
            ->orWhere('id', is_numeric($dinasInput) ? (int)$dinasInput : 0)
            ->first();

        if (!$opd) {
            $userEmp = Auth::user()->loadMissing('employments.perangkatDaerah');
            $opd = $userEmp->employments->first()?->perangkatDaerah;
        }

        if (!$opd) {
            $opd = PerangkatDaerah::firstOrCreate(
                ['name' => $dinasInput],
                ['code' => strtoupper(Str::slug($dinasInput))]
            );
        }

        $perangkatDaerahId = $opd->id;

        // Ambil bidang penanganan dari master layanan
        $service = Services::find($request->category_id);
        $bidangId = $service?->bidang_id;

        // Jika layanan belum memiliki bidang_id, pasang ke bidang teknis utama
        if (!$bidangId) {
            $bidangId = Bidang::where('name', 'like', '%aplikasi%')
                ->orWhere('name', 'like', '%aptika%')
                ->orWhere('name', 'like', '%jaringan%')
                ->orWhere('name', 'like', '%tik%')
                ->value('id') ?? Bidang::first()?->id ?? 1;
        }

        // Ambil ID status 'Baru' yang sah (hindari create acak ID 9 atau 13)
        $statusId = TicketStatuses::where('code', 'baru')
            ->orWhere('name', 'like', '%baru%')
            ->value('id') ?? 1;

        // 3. Simpan ke Database via Transaksi
        (new Tickets)->getConnection()->transaction(function () use (
            $request, 
            $userId, 
            $ticketNumber, 
            $subject, 
            $fullDescription, 
            $perangkatDaerahId, 
            $bidangId,
            $statusId
        ) {
            $ticket = Tickets::create([
                'ticket_number'       => $ticketNumber,
                'public_token'        => Str::random(32),
                'user_id'             => $userId,
                'service_id'          => (int) $request->category_id,
                'ticket_status_id'    => $statusId,
                'subject'             => $subject,
                'description'         => $fullDescription,
                'perangkat_daerah_id' => $perangkatDaerahId,
                'bidang_id'           => $bidangId,
            ]);

            // Simpan Lampiran jika ada
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $path = $file->store('tickets/attachments', 'public');

                $fileRecord = Files::create([
                    'disk'            => 'public',
                    'path'            => $path,
                    'original_name'   => $file->getClientOriginalName(),
                    'mime_type'       => $file->getClientMimeType(),
                    'size_bytes'      => $file->getSize(),
                    'checksum_sha256' => hash_file('sha256', $file->getRealPath()),
                    'uploaded_by'     => $userId,
                ]);

                if (method_exists($ticket, 'ticketFiles')) {
                    $ticket->ticketFiles()->attach($fileRecord->id);
                } else {
                    TicketFile::create([
                        'ticket_id' => $ticket->id,
                        'file_id'   => $fileRecord->id,
                    ]);
                }
            }

            // Catat Timeline Histori Status
            $history = new TicketStatusHistory();
            $cols = $history->getConnection()->getSchemaBuilder()->getColumnListing($history->getTable());

            $history->ticket_id = $ticket->id;
            $history->ticket_status_id = $statusId;
            $history->note = "Permohonan tiket baru diajukan dari {$request->dinas}.";

            if (in_array('user_id', $cols)) {
                $history->user_id = $userId;
            } elseif (in_array('changed_by', $cols)) {
                $history->changed_by = $userId;
            }

            if (!in_array('created_at', $cols)) {
                $history->timestamps = false;
            }

            $history->save();
        });

        return redirect()->route('user.tiket')->with('success', "Permohonan berhasil dikirim dengan nomor {$ticketNumber}. Menunggu proses verifikasi dan disposisi oleh Helpdesk.");
    }

    /**
     * Halaman Riwayat Tiket Saya
     */
    public function myTickets(Request $request): View
    {
        $userId = Auth::id();

        $query = Tickets::with([
                'status', 
                'service',
                'tasks.assignees',
                'perangkatDaerah',
                'bidang'
            ])
            ->where('user_id', $userId);

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

        $tickets = $query->latest('created_at')->get()->map(function ($ticket) {
            $ticket->technicians = $ticket->tasks
                ->flatMap(fn($task) => $task->assignees->pluck('name'))
                ->unique()
                ->values()
                ->toArray();

            return $ticket;
        });

        $allUserTickets = Tickets::where('user_id', $userId)->get(['id', 'ticket_status_id']);
        $totalCount    = $allUserTickets->count();
        $activeCount   = $allUserTickets->whereIn('ticket_status_id', [1, 2, 3])->count();
        $solvedCount   = $allUserTickets->where('ticket_status_id', 4)->count();
        $rejectedCount = $allUserTickets->where('ticket_status_id', 5)->count();

        $statuses = TicketStatuses::orderBy('sort_order', 'asc')->get();

        return view('user.tiket', compact(
            'tickets',
            'totalCount',
            'activeCount',
            'solvedCount',
            'rejectedCount',
            'statuses'
        ));
    }

    /**
     * Chat Diskusi Tiket
     */
    public function chat(Request $request): View
    {
        $userId = Auth::id();

        $tickets = Tickets::with(['status', 'service', 'tasks.assignees'])
            ->where('user_id', $userId)
            ->latest('updated_at')
            ->get()
            ->map(function ($ticket) use ($userId) {
                $ticket->assigned_technicians = $ticket->tasks
                    ->flatMap(fn($task) => $task->assignees)
                    ->unique('id')
                    ->values();

                $ticket->latest_message = TicketMessage::where('ticket_id', $ticket->id)
                    ->latest('created_at')
                    ->first();

                $ticket->unread_count = TicketMessage::where('ticket_id', $ticket->id)
                    ->where('user_id', '!=', $userId)
                    ->whereNull('read_at')
                    ->count();

                return $ticket;
            });

        $activeTicketId = $request->query('ticket_id') ?? ($tickets->first()?->id ?? null);
        $activeTicket = $tickets->firstWhere('id', (int) $activeTicketId);

        $messages = collect();
        if ($activeTicket) {
            TicketMessage::where('ticket_id', $activeTicket->id)
                ->where('user_id', '!=', $userId)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            $messages = TicketMessage::join('users', 'users.id', '=', 'ticket_messages.user_id')
                ->where('ticket_messages.ticket_id', $activeTicket->id)
                ->select(
                    'ticket_messages.id',
                    'ticket_messages.user_id',
                    'ticket_messages.body',
                    'ticket_messages.created_at',
                    'users.name as sender_name'
                )
                ->orderBy('ticket_messages.created_at', 'asc')
                ->get();
        }

        return view('user.chat', compact('tickets', 'activeTicket', 'messages'));
    }
}
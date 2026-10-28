<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Files;
use App\Models\Tickets;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;

class UserController extends Controller
{
    public function createTicket(): View
    {
        // 1. Ambil Kategori Layanan dari database
        if (Schema::hasTable('service_categories')) {
            $categories = DB::table('service_categories')->orderBy('name', 'asc')->get();
        } else {
            $categories = DB::table('services')->orderBy('name', 'asc')->get();
        }

        // 2. Daftar Perangkat Daerah / Dinas
        $dinasList = [
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

        return view('user.create-ticket', compact('categories', 'dinasList'));
    }

    /**
     * Simpan Pengajuan Tiket ke Database
     */
    public function storeTicket(Request $request)
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

        // Generate Nomor Tiket Otomatis (Format: TCK-YYYYMMDD-XXX)
        $today = now()->format('Ymd');
        $countToday = Tickets::whereDate('created_at', today())->count() + 1;
        $ticketNumber = 'TCK-' . $today . '-' . str_pad($countToday, 3, '0', STR_PAD_LEFT);

        // Jika subjek kosong, ambil 60 karakter pertama dari deskripsi
        $subject = trim($request->subject ?? '') !== '' 
            ? $request->subject 
            : Str::limit(strip_tags($request->description), 60);

        // Sisipkan informasi Dinas ke dalam deskripsi agar mudah dibaca Helpdesk
        $fullDescription = "[Instansi: {$request->dinas}]\n" . $request->description;

        DB::transaction(function () use ($request, $userId, $ticketNumber, $subject, $fullDescription) {
            // Simpan tiket baru (Status 1: Baru)
            $ticket = Tickets::create([
                'ticket_number'    => $ticketNumber,
                'public_token'     => Str::random(32),
                'user_id'          => $userId,
                'service_id'       => (int) $request->category_id,
                'ticket_status_id' => 1,
                'subject'          => $subject,
                'description'      => $fullDescription,
            ]);

            // Simpan berkas lampiran jika ada
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
                    DB::table('ticket_files')->insert([
                        'ticket_id' => $ticket->id,
                        'file_id'   => $fileRecord->id,
                    ]);
                }
            }

            // Catat status awal di riwayat
            if (Schema::hasTable('ticket_status_histories')) {
                DB::table('ticket_status_histories')->insert([
                    'ticket_id'        => $ticket->id,
                    'ticket_status_id' => 1,
                    'changed_by'       => $userId,
                    'note'             => "Permohonan tiket baru diajukan dari {$request->dinas}.",
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]);
            }
        });

        return redirect()->route('user.tiket')->with('success', "Permohonan berhasil dikirim dengan nomor {$ticketNumber}. Menunggu proses triage oleh Helpdesk Bidang.");
    }

    public function myTickets(Request $request): View
    {
        $userId = Auth::id();

        $query = Tickets::with(['status', 'service'])
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
            $task = DB::table('tickets_task')->where('ticket_id', $ticket->id)->first();
            $assignedTechs = [];

            if ($task) {
                $techIds = DB::table('ticket_task_assignees')
                    ->where('ticket_task_id', $task->id)
                    ->pluck('user_id')
                    ->toArray();

                $assignedTechs = User::whereIn('id', $techIds)->pluck('name')->toArray();
            }

            $ticket->technicians = $assignedTechs;
            return $ticket;
        });

        $allUserTickets = Tickets::where('user_id', $userId)->get();
        $totalCount    = $allUserTickets->count();
        $activeCount   = $allUserTickets->whereIn('ticket_status_id', [1, 2, 3])->count();
        $solvedCount   = $allUserTickets->where('ticket_status_id', 4)->count();
        $rejectedCount = $allUserTickets->where('ticket_status_id', 5)->count();

        $statuses = DB::table('ticket_statuses')->orderBy('sort_order', 'asc')->get();

        return view('user.tiket', compact(
            'tickets',
            'totalCount',
            'activeCount',
            'solvedCount',
            'rejectedCount',
            'statuses'
        ));
    }

    public function chat(Request $request): View
    {
        $userId = Auth::id();

        // 1. Ambil seluruh tiket milik user
        $tickets = Tickets::with(['status', 'service'])
            ->where('user_id', $userId)
            ->latest('updated_at')
            ->get()
            ->map(function ($ticket) use ($userId) {
                // Ambil teknisi yang ditugaskan pada tiket ini
                $task = DB::table('tickets_task')->where('ticket_id', $ticket->id)->first();
                $technicians = [];

                if ($task) {
                    $techIds = DB::table('ticket_task_assignees')
                        ->where('ticket_task_id', $task->id)
                        ->pluck('user_id')
                        ->toArray();

                    $technicians = User::whereIn('id', $techIds)->get(['id', 'name', 'email']);
                }

                $ticket->assigned_technicians = $technicians;

                // Ambil preview pesan terakhir
                $ticket->latest_message = DB::table('ticket_messages')
                    ->where('ticket_id', $ticket->id)
                    ->latest('created_at')
                    ->first();

                // Hitung pesan yang belum dibaca dari teknisi
                $ticket->unread_count = DB::table('ticket_messages')
                    ->where('ticket_id', $ticket->id)
                    ->where('user_id', '!=', $userId)
                    ->whereNull('read_at')
                    ->count();

                return $ticket;
            });

        // 2. Tentukan tiket yang sedang dibuka (berdasarkan parameter query atau tiket pertama)
        $activeTicketId = $request->query('ticket_id') ?? ($tickets->first()?->id ?? null);
        $activeTicket = $tickets->firstWhere('id', (int) $activeTicketId);

        $messages = collect();
        if ($activeTicket) {
            // Tandai pesan masuk pada tiket ini sebagai sudah dibaca
            DB::table('ticket_messages')
                ->where('ticket_id', $activeTicket->id)
                ->where('user_id', '!=', $userId)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            // Ambil daftar pesan terurut dari waktu terlama ke terbaru
            $messages = DB::table('ticket_messages')
                ->join('users', 'users.id', '=', 'ticket_messages.user_id')
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
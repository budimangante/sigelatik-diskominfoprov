<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Bidang;
use App\Models\PerangkatDaerah;
use App\Models\TicketStatuses;
use App\Models\Tickets;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DaftarTiketController extends Controller
{
    /**
     * Tampilkan Daftar Tiket di Super Admin (Bebas N+1 & Bebas Error SQL)
     */
    public function index(Request $request): View
    {
        $search   = $request->query('search');
        $statusId = $request->query('status_id');
        $opdId    = $request->query('perangkat_daerah_id');

        $statusList          = TicketStatuses::orderBy('id', 'asc')->get();
        $perangkatDaerahList = PerangkatDaerah::orderBy('name', 'asc')->get();
        $bidangList          = Bidang::orderBy('name', 'asc')->get();
        
        $teknisiList = User::whereHas('roles', function ($q) {
            $q->where('name', 'like', '%teknisi%');
        })->orderBy('name', 'asc')->get();

        // Query relasi murni tanpa memanggil kolom yang tidak ada di MySQL
        $tickets = Tickets::with([
                'user',
                'perangkatDaerah',
                'bidang',
                'status',
                'tasks.assignees',
            ])
            ->when($search, function ($q, $search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('ticket_number', 'like', "%{$search}%")
                        ->orWhere('subject', 'like', "%{$search}%")
                        ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($statusId, fn($q, $id) => $q->where('ticket_status_id', $id))
            ->when($opdId, fn($q, $id) => $q->where('perangkat_daerah_id', $id))
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        // Hubungkan data teknisi yang bertugas dari memori
        foreach ($tickets as $ticket) {
            $ticket->assigned_technician = $ticket->tasks->first()?->assignees->first();
        }

        return view('superadmin.daftar-ticket', compact(
            'tickets',
            'statusList',
            'perangkatDaerahList',
            'bidangList',
            'teknisiList',
            'search',
            'statusId',
            'opdId'
        ));
    }

    /**
     * Super Admin: Buat Tiket Baru
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title'               => 'required|string|max:255',
            'description'         => 'required|string',
            'perangkat_daerah_id' => 'required|exists:perangkat_daerah,id',
            'bidang_id'           => 'required|exists:bidang,id',
            'technician_id'       => 'nullable|exists:users,id',
            'user_id'             => 'nullable|exists:users,id',
        ], [
            'title.required'               => 'Judul permohonan / keluhan tiket wajib diisi.',
            'description.required'         => 'Rincian deskripsi masalah tiket wajib diisi.',
            'perangkat_daerah_id.required' => 'Pilih Perangkat Daerah / Dinas pemilik tiket.',
            'bidang_id.required'           => 'Pilih Bidang penanganan tujuan.',
        ]);

        $ticketCode = 'TCK-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4));

        // Simpan tiket HANYA dengan kolom fisik yang valid di MySQL
        $ticket = Tickets::create([
            'ticket_number'       => $ticketCode,
            'public_token'        => Str::random(32),
            'user_id'             => $validated['user_id'] ?? auth()->id(),
            'service_id'          => 1,
            'ticket_status_id'    => 1,
            'subject'             => $validated['title'],
            'description'         => $validated['description'],
            'perangkat_daerah_id' => $validated['perangkat_daerah_id'],
            'bidang_id'           => $validated['bidang_id'],
        ]);

        // Hubungkan Teknisi via model TicketTask
        if (!empty($validated['technician_id'])) {
            $task = $ticket->tasks()->create([
                'ticket_status_id' => 1,
                'service_id'       => 1,
                'created_by'       => auth()->id(),
                'started_at'       => now(),
            ]);

            $task->assignees()->attach($validated['technician_id']);
        }

        return redirect()->route('daftar.ticket')->with('success', "Tiket baru [{$ticketCode}] berhasil diterbitkan.");
    }

    /**
     * Super Admin: Disposisi Bidang, Status & Penugasan Teknisi
     */
    public function update(Request $request, Tickets $ticket): RedirectResponse
    {
        $validated = $request->validate([
            'ticket_status_id'    => 'required|exists:ticket_statuses,id',
            'bidang_id'           => 'required|exists:bidang,id',
            'perangkat_daerah_id' => 'required|exists:perangkat_daerah,id',
            'technician_id'       => 'nullable|exists:users,id',
        ], [
            'ticket_status_id.required' => 'Pilih status tiket yang baru.',
            'bidang_id.required'        => 'Pilih bidang yang bertanggung jawab.',
        ]);

        // Update HANYA kolom yang ada di database
        $ticket->update([
            'ticket_status_id'    => $validated['ticket_status_id'],
            'bidang_id'           => $validated['bidang_id'],
            'perangkat_daerah_id' => $validated['perangkat_daerah_id'],
        ]);

        // Update Penugasan Teknisi
        if (array_key_exists('technician_id', $validated)) {
            $task = $ticket->tasks()->firstOrCreate(
                ['ticket_id' => $ticket->id],
                [
                    'ticket_status_id' => $validated['ticket_status_id'],
                    'service_id'       => $ticket->service_id ?? 1,
                    'created_by'       => auth()->id(),
                    'started_at'       => now(),
                ]
            );

            if (!empty($validated['technician_id'])) {
                $task->assignees()->sync([$validated['technician_id']]);
            } else {
                $task->assignees()->detach();
            }
        }

        return redirect()->route('daftar.ticket')->with('success', "Informasi tiket [{$ticket->ticket_number}] berhasil diperbarui.");
    }

    /**
     * Super Admin: Hapus Tiket
     */
    public function destroy(Tickets $ticket): RedirectResponse
    {
        $code = $ticket->ticket_number;

        foreach ($ticket->tasks as $task) {
            $task->assignees()->detach();
            $task->delete();
        }

        $ticket->delete();

        return redirect()->route('daftar.ticket')->with('success', "Tiket [{$code}] berhasil dihapus dari sistem.");
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Tickets;
use Illuminate\Http\Request;

class LacakTicketController extends Controller
{
    public function track(Request $request)
    {
        // 1. Validasi input
        $request->validate([
            'ticket_number' => 'required|string|max:50',
        ], [
            'ticket_number.required' => 'Silakan masukkan nomor tiket terlebih dahulu.',
        ]);

        $ticketNumber = trim($request->input('ticket_number'));

        // 2. Query tiket beserta relasi penting untuk timeline
        $ticket = Tickets::with([
                'status',
                'service',
                'statusHistories.status', // riwayat status berurutan
            ])
            ->where('ticket_number', $ticketNumber)
            ->first();

        // 3. Jika tidak ditemukan
        if (!$ticket) {
            return back()
                ->withInput()
                ->with('error', "Tiket dengan nomor {$ticketNumber} tidak ditemukan. Periksa kembali nomor tiket Anda.");
        }

        // 4. Return ke view hasil pelacakan
        return view('beranda.lacak-tiket', compact('ticket'));
    }

    /**
     * Alternatif menggunakan URL unik/token (misal dari link email/WhatsApp)
     * Contoh URL: /tiket/lacak/{public_token}
     */
    public function trackByToken(string $token)
    {
        $ticket = Tickets::with(['status', 'service', 'statusHistories.status'])
            ->where('public_token', $token)
            ->firstOrFail();

        return view('beranda.lacak-tiket', compact('ticket'));
    }
}

<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Tickets;
use App\Models\TicketStatusHistory;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class ForwardController extends Controller
{
    public function index(): View
    {
        $alreadyForwardedIds = TicketStatusHistory::where('note', 'like', 'Diteruskan oleh Bidang ke:%')
            ->pluck('ticket_id')
            ->toArray();

        $pendingTickets = Tickets::whereHas('statusHistories', function ($query) {
                $query->where('note', 'like', '%diteruskan ke Helpdesk Bidang%');
            })
            ->whereNotIn('id', $alreadyForwardedIds)
            ->with(['user', 'statusHistories'])
            ->latest('id')
            ->get();

        $forwardedTickets = TicketStatusHistory::with(['tickets', 'user'])
            ->where('note', 'like', 'Diteruskan oleh Bidang ke:%')
            ->orderByDesc('id')
            ->get()
            ->map(function ($history) {
                preg_match('/Diteruskan oleh Bidang ke:\s*([^.|]+)/', $history->note ?? '', $matches);
                $targetDivision = isset($matches[1]) ? trim($matches[1]) : 'Bidang Terkait';

                return (object) [
                    'ticket_number'   => $history->tickets->ticket_number ?? '-',
                    'subject'         => $history->tickets->subject ?? '-',
                    'target_division' => $targetDivision,
                    'notes'           => $history->note ?? '-',
                    'time'            => '#' . $history->id,
                ];
            });

        return view('bidang.forward', compact('pendingTickets', 'forwardedTickets'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ticket_id'        => 'required|exists:tickets,id',
            'target_division'  => 'required|string|max:100',
            'technician_note'  => 'nullable|string',
            'notes'            => 'nullable|string|max:1000',
        ]);

        $ticket = Tickets::findOrFail($validated['ticket_id']);

        $finalNote = "Diteruskan oleh Bidang ke: {$validated['target_division']}. " . 
                     ($validated['notes'] ? "[Catatan: {$validated['notes']}]" : "");

        TicketStatusHistory::create([
            'ticket_id'        => $ticket->id,
            'ticket_status_id' => $ticket->ticket_status_id ?? 1,
            'changed_by'       => Auth::id(),
            'note'             => $finalNote,
        ]);

        return redirect()->route('bidang.forward')
            ->with('success', 'Tiket berhasil diteruskan ke bidang tujuan!');
    }

    public function history(Request $request): View
    {
        $user = Auth::user();
        $bidangId = $user->bidang_id ?? null;

        $query = Tickets::with(['status', 'service', 'statusHistories'])
            ->when($bidangId, function ($q) use ($bidangId) {
                $q->whereHas('service', function ($s) use ($bidangId) {
                    $s->where('bidang_id', $bidangId);
                });
            })
            ->where(function ($q) {
                $q->whereIn('ticket_status_id', [4, 5]) 
                ->orWhereHas('statusHistories', function ($sub) {
                    $sub->where('note', 'like', '%Diteruskan%');
                });
            });

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        $historyTickets = $query->latest('updated_at')->get()->map(function ($ticket) {
            $statusId = $ticket->ticket_status_id;
            $latestHistory = $ticket->statusHistories?->last();
            $latestNote = $latestHistory?->note ?? '';

            if (str_contains($latestNote, 'Diteruskan')) {
                preg_match('/Diteruskan (?:oleh Bidang )?ke:\s*([^.|]+)/', $latestNote, $matches);
                $target = isset($matches[1]) ? trim($matches[1]) : 'bidang lain';
                $event = 'Diteruskan ke ' . $target;
            } elseif ($statusId == 4) {
                $event = 'Selesai (Solved)';
            } elseif ($statusId == 5) {
                $event = 'Dibatalkan';
            } else {
                $event = $ticket->status->name ?? 'Selesai';
            }

            $time = $ticket->solved_at ?? $ticket->cancelled_at ?? $ticket->updated_at;

            return (object) [
                'id'            => $ticket->id,
                'ticket_number' => $ticket->ticket_number,
                'subject'       => $ticket->subject,
                'event'         => $event,
                'time'          => $time ? \Carbon\Carbon::parse($time)->translatedFormat('d M Y, H:i') : '-',
            ];
        });

        if ($request->filled('event')) {
            $historyTickets = $historyTickets->filter(function ($item) use ($request) {
                if ($request->event === 'solved') return str_contains($item->event, 'Solved');
                if ($request->event === 'cancelled') return str_contains($item->event, 'Dibatalkan');
                if ($request->event === 'forwarded') return str_contains($item->event, 'Diteruskan');
                return true;
            });
        }

        return view('bidang.history-ticket', compact('historyTickets'));
    }
}
<?php

namespace App\Http\Controllers\Web;

use App\Events\TicketMessageSent;
use App\Http\Controllers\Controller;
use App\Models\Tickets;
use App\Models\TicketMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class TicketMessageController extends Controller
{
    public function index(Tickets $ticket): JsonResponse
    {
        $userId = Auth::id();

        TicketMessage::where('ticket_id', $ticket->id)
            ->where('user_id', '!=', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $messages = TicketMessage::where('ticket_id', $ticket->id)
            ->with('user:id,name')
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($msg) use ($userId) {
                return [
                    'id'             => $msg->id,
                    'ticket_id'      => $msg->ticket_id,
                    'ticket_task_id' => $msg->ticket_task_id,
                    'user_id'        => $msg->user_id,
                    'user_name'      => $msg->user?->name ?? 'Pelapor',
                    'is_me'          => $msg->user_id === $userId,
                    'body'           => $msg->body,
                    'read_at'        => $msg->read_at ? ($msg->read_at instanceof \Carbon\Carbon ? $msg->read_at->toIso8601String() : $msg->read_at) : null,
                    'created_at'     => $msg->created_at ? $msg->created_at->format('H:i') : '-',
                    'date'           => $msg->created_at ? $msg->created_at->translatedFormat('d M Y') : '-',
                ];
            });

        return response()->json([
            'success'  => true,
            'messages' => $messages,
        ]);
    }

    /**
     * Simpan pesan baru & siarkan secara realtime via Reverb.
     */
    public function store(Request $request, Tickets $ticket): JsonResponse
    {
        $validated = $request->validate([
            'body'           => 'required|string|max:5000',
            'ticket_task_id' => 'nullable|exists:tickets_task,id',
        ]);

        $message = TicketMessage::create([
            'ticket_id'      => $ticket->id,
            'ticket_task_id' => $validated['ticket_task_id'] ?? null,
            'user_id'        => Auth::id(),
            'body'           => $validated['body'],
        ]);

        // Siarkan event ke channel Reverb
        broadcast(new TicketMessageSent($message))->toOthers();

        return response()->json([
            'success' => true,
            'message' => [
                'id'             => $message->id,
                'ticket_id'      => $message->ticket_id,
                'ticket_task_id' => $message->ticket_task_id,
                'user_id'        => $message->user_id,
                'user_name'      => Auth::user()->name,
                'is_me'          => true,
                'body'           => $message->body,
                'read_at'        => null,
                'created_at'     => $message->created_at->format('H:i'),
                'date'           => $message->created_at->translatedFormat('d M Y'),
            ],
        ]);
    }
}
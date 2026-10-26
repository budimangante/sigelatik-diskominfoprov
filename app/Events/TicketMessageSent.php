<?php

namespace App\Events;

use App\Models\TicketMessage;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketMessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public TicketMessage $message;

    public function __construct(TicketMessage $message)
    {
        // Load relasi user agar nama & role pengirim ikut terbawa ke frontend
        $this->message = $message->load('user:id,name,role');
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('tickets.' . $this->message->ticket_id . '.chat'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    public function broadcastWith(): array
    {
        return [
            'id'             => $this->message->id,
            'ticket_id'      => $this->message->ticket_id,
            'ticket_task_id' => $this->message->ticket_task_id,
            'user_id'        => $this->message->user_id,
            'user_name'      => $this->message->user->name,
            'body'           => $this->message->body,
            'read_at'        => $this->message->read_at?->toISOString(),
            'created_at'     => $this->message->created_at->format('H:i'),
            'date'           => $this->message->created_at->translatedFormat('d M Y'),
        ];
    }
}
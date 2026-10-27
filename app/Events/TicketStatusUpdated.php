<?php

namespace App\Events;

use App\Models\Tickets;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $ticket;
    public $statusName;

    public function __construct(Tickets $ticket, string $statusName)
    {
        $this->ticket = $ticket;
        $this->statusName = $statusName;
    }

    public function broadcastOn()
    {
        return new Channel('tickets');
    }
}
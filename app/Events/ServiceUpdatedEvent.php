<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ServiceUpdatedEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $message;
    public string $type;

    public function __construct(string $message, string $type = 'success')
    {
        $this->message = $message;
        $this->type = $type;
    }

    /**
     * Channel publik untuk notifikasi operasional bidang/layanan
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('services-channel'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'service.event';
    }
}
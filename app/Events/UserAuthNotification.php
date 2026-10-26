<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserAuthNotification implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $status;
    public string $message;
    public ?string $userName;

    public function __construct(string $status, string $message, ?string $userName = null)
    {
        $this->status   = $status;
        $this->message  = $message;
        $this->userName = $userName;
    }

    /**
     * Channel siaran publik yang didengar oleh frontend.
     */
    public function broadcastOn(): array
    {
return [
        new PrivateChannel('auth-tracker'),
    ];
    }

    /**
     * Nama event yang didengar oleh Echo (.auth.status).
     */
    public function broadcastAs(): string
    {
        return 'auth.status';
    }
}
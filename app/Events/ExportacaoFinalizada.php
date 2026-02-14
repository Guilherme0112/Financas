<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ExportacaoFinalizada implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $userId, 
        public ?int $exportacaoId = null,
        public ?string $error = null    
    ){}

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('users.' . $this->userId)
        ];
    }

    public function broadcastAs()
    {
        return "ExportacaoFinalizada";
    }

    public function broadcastWith()
    {
        return [
            'exportacaoId' => $this->exportacaoId,
            'error' => $this->error,
        ];
    }
}

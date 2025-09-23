<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ExcelImportCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $escenarioId;

    /**
     * Create a new event instance.
     */
    public function __construct($escenarioId)
    {
        $this->escenarioId = $escenarioId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn()
    {
        return new Channel("excel-import-escenario.{$this->escenarioId}");
    }

    public function broadcastAs()
    {
        return 'ExcelImportCompleted';
    }
}

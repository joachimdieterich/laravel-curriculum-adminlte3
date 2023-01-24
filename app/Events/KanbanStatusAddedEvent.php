<?php

namespace App\Events;

use App\KanbanStatus;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class KanbanStatusAddedEvent implements  ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private KanbanStatus $kanbanStatus;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(KanbanStatus $kanbanStatus)
    {
        $this->kanbanStatus = $kanbanStatus;
    }

    public function broadcastWhen()
    {
        return env('PUSHER_APP_ACTIVE', false);
    }


    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PresenceChannel('Presence.App.Kanban.' . $this->kanbanStatus->kanban_id);
    }

    public function broadcastAs(){
        return 'kanbanStatusAdded';
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastWith(){

        return [
            'user' => auth()->user()->only(['id', 'firstname', 'lastname']),
            'message' =>  $this->kanbanStatus
        ];
    }
}

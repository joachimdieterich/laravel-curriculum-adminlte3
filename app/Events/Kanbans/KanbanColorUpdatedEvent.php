<?php

namespace App\Events\Kanbans;

use App\Kanban;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class KanbanColorUpdatedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private Kanban $kanban;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Kanban $kanban)
    {
        $this->kanban = $kanban;
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
        return new PresenceChannel('Presence.App.Kanban.' . $this->kanban->id);
    }

    public function broadcastAs(){
        return 'kanbanColorUpdated';
    }


    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastWith(){

        return [
            'user' => auth()->user()->only(['id', 'firstname', 'lastname']),
            'message' =>  $this->kanban->color
        ];
    }
}

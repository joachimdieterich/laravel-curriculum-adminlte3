<?php

namespace App\Events\Kanbans;

use App\Kanban;
use App\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class KanbanStatusMovedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private Kanban $kanban;
    private User $user;

    /**
     * @param Kanban $kanban
     */
    public function __construct(Kanban $kanban)
    {
        $this->kanban = $kanban;
    }

    public function broadcastWhen()
    {
        return env('WEBSOCKET_APP_ACTIVE', false);
    }


    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PresenceChannel('App.Kanban.' . $this->kanban->id);
    }

    public function broadcastAs(){
        return 'kanbanStatusMoved';
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastWith(){

        return [
            'user' => auth()->user()->only(['id', 'firstname', 'lastname']),
            'message' => $this->kanban
                ->where('id', $this->kanban->id)
                ->with(['statuses'])
                ->get()->first()
        ];
    }
}

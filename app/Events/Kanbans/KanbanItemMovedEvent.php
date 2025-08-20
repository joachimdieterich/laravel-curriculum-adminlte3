<?php

namespace App\Events\Kanbans;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class KanbanItemMovedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private Array $columns;

    /**
     * @param  $colums
     */
    public function __construct(Array $columns)
    {
        $this->columns = $columns;
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
        return new PresenceChannel('App.Kanban.' . $this->columns[0]['kanban_id']);
    }

    public function broadcastAs(){
        return 'kanbanItemMoved';
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastWith(){

        return [
            'message' => $this->columns
        ];
    }
}

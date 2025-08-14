<?php

namespace App\Events\Kanbans;

use App\KanbanItem;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class KanbanItemReloadEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private KanbanItem $kanbanItem;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(KanbanItem $kanbanItem)
    {
        $this->kanbanItem = $kanbanItem;
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
        return new PresenceChannel('Presence.App.Kanban.' . $this->kanbanItem->kanban_id);
    }

    public function broadcastAs(){
        return 'kanbanItemReload';
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastWith(){

        return [
            'user' => auth()->user()->only(['id', 'firstname', 'lastname']),
            'message' =>  $this->kanbanItem
                ->where('id', $this->kanbanItem->id)
                ->with([
                    'comments',
                    'comments.user',
                    'comments.likes',
                    'likes',
                    'mediaSubscriptions.medium',
                    'owner',
                ])
                ->get()->first()
        ];
    }
}


<?php

namespace App\Services\Websocket;

use Illuminate\Broadcasting\Channel;
use Illuminate\Database\Eloquent\BroadcastableModelEventOccurred;

trait BroadcastsEvents
{
    use \Illuminate\Database\Eloquent\BroadcastsEvents;

    public function broadcastOn($event): array
    {
        if (! config('broadcasting.active')) {
            return [];
        }

        return [
            new Channel($this->broadcastChannel()),
        ];
    }

    public function broadcastWith($event): array
    {
        return [
            'model' => $event === 'deleted' ? $this : $this->withRelations(),
        ];
    }

    protected function newBroadcastableEvent(string $event): BroadcastableModelEventOccurred
    {
        return new BroadcastableModelEventOccurred(
            $this, $event
        )->dontBroadcastToCurrentUser();
    }
}

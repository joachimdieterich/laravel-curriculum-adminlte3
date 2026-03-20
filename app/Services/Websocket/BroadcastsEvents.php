<?php

namespace App\Services\Websocket;

use Illuminate\Broadcasting\PresenceChannel;

trait BroadcastsEvents
{
    use \Illuminate\Database\Eloquent\BroadcastsEvents;

    public function broadcastOn($event): array
    {
        if (!config('broadcasting.active')) {
            return [];
        }

        return [
            new PresenceChannel($this->broadcastChannel())
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'model' => $this->withRelations(),
        ];
    }
}
<?php

namespace App\Services\Websocket;

use Illuminate\Broadcasting\PresenceChannel;

trait BroadcastsEvents
{
    use \Illuminate\Database\Eloquent\BroadcastsEvents;

    public function broadcastOn($event): array
    {
        if (!env('WEBSOCKET_APP_ACTIVE', false)) {
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
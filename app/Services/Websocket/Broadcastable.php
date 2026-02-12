<?php

namespace App\Services\Websocket;

interface Broadcastable {
    public function withRelations(): self|null;
}
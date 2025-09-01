<?php

namespace App;

use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Database\Eloquent\BroadcastsEvents;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Maize\Markable\Mark;
use Maize\Markable\Models\Like as BasicLike;

class Like extends BasicLike
{
//    use BroadcastsEvents;

    public function broadcastOn(): array
    {
        return [
            new PresenceChannel($this->broadcastChannel())
        ];
    }

    public static function find(Model $markable, Model $user, ?string $value = null): Model
    {
        return static::where([
            app(static::class)->getUserIdColumn() => $user->getKey(),
            'markable_id' => $markable->getKey(),
            'markable_type' => $markable->getMorphClass(),
            'value' => $value,
        ])->first();
    }

    public static function add(
        Model $markable,
        Model $user,
        ?string $value = null,
        array $metadata = []
    ): Mark {
        $markable->touch();

        return parent::add($markable, $user, $value, $metadata);
    }

    public static function remove(Model $markable, Model $user, ?string $value = null)
    {
        $markable->touch();

        return parent::remove($markable, $user, $value);
    }
}

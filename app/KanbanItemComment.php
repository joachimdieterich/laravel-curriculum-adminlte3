<?php

namespace App;

use DateTimeInterface;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Database\Eloquent\BroadcastsEvents;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Maize\Markable\Markable;

class KanbanItemComment extends Model
{
    use HasFactory, Markable, BroadcastsEvents;

    protected $casts = [
        'created_at' => 'datetime:d.m.Y H:i',
        'updated_at' => 'datetime:d.m.Y H:i',
    ];

    protected $guarded = [];

    protected static $marks = [
        Like::class,
    ];

    public function broadcastOn($event): array
    {
        $broadcastChannelName = str_replace('\\', '.', get_class($this)) . 's.' . $this->kanban_item_id;

        return [
            new PresenceChannel($broadcastChannelName)
        ];
    }

    public function broadcastWith($event): array
    {
        return [
            'id' => $this->id,
            'model' => $this->with(
                'user',
                'likes',
            )->find($this->id),
        ];
    }

    /**
     * Prepare a date for array / JSON serialization.
     *
     * @param  \DateTimeInterface  $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function kanbanItem()
    {
        return $this->belongsTo(KanbanItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

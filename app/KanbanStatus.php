<?php

namespace App;

use DateTimeInterface;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Database\Eloquent\BroadcastsEvents;
use Illuminate\Database\Eloquent\Model;

class KanbanStatus extends Model
{
    use BroadcastsEvents;

    protected $guarded = [];

    public function broadcastOn($event): array
    {
        return [
            new PresenceChannel($this->broadcastChannel())
        ];
    }

    protected $casts = [
        'locked' => 'boolean',
        'editable' => 'boolean',
        'visibility' => 'boolean',
        'updated_at' => 'datetime',
        'created_at' => 'datetime',
        'visible_from' => 'datetime',
        'visible_until' => 'datetime',
    ];

    /**
     * Prepare a date for array / JSON serialization.
     *
     * @param \DateTimeInterface $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function items()
    {
        return $this->hasMany('App\KanbanItem')->orderBy('order_id');
    }

    public function owner()
    {
        return $this->hasOne('App\User', 'id', 'owner_id');
    }

    public function kanban()
    {
        return $this->hasOne('App\Kanban', 'id', 'kanban_id');
    }

    /**
     * Accessor that mimics Eloquent dynamic property.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getEditorsAttribute()
    {
        if (!$this->relationLoaded('editors')) {
            $layers = User::whereIn('id', $this->editors_ids)->get();

            $this->setRelation('editors', $layers);
        }

        return $this->getRelation('editors');
    }

    /**
     * Access editors relation query.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function editors()
    {
        return User::whereIn('id', $this->editors_ids);
    }

    /**
     * Accessor for editors_ids property.
     *
     * @return array
     */
    public function getEditorsIdsAttribute($commaSeparatedIds)
    {

        return explode(',', $commaSeparatedIds);
    }

    /**
     * Mutator for layer_ids property.
     *
     * @param array|string|id $ids
     * @return void
     */
    public function setEditorsIdsAttribute($ids)
    {
        $this->attributes['editors_ids'] = is_string($ids) ? $ids : implode(',', array_filter($ids)); // array filter removes empty entries.
    }


    public function isAccessible()
    {
        return $this->kanban->isAccessible();
    }
}

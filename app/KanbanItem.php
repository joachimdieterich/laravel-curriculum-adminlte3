<?php

namespace App;

use DateTimeInterface;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Database\Eloquent\BroadcastsEvents;
use Maize\Markable\Markable;
use Illuminate\Database\Eloquent\Model;
use Mews\Purifier\Casts\CleanHtml;

class KanbanItem extends Model
{
    use BroadcastsEvents;
    use Markable;

    protected $guarded = [];

    protected $casts = [
        'description' => CleanHtml::class, // cleans both when getting and setting the value
        'locked' => 'boolean',
        'editable' => 'boolean',
        'replace_links' => 'boolean',
        'visibility' => 'boolean',
        'updated_at' => 'datetime',
        'created_at'  => 'datetime',
        'due_date' => 'datetime',
        'visible_from'  => 'datetime',
        'visible_until'  => 'datetime',
    ];

    protected static array $marks = [
        Like::class,
    ];

    public function broadcastOn($event): array
    {
        $defaultChannels = [
            new PresenceChannel($this->broadcastChannel())
        ];

        $diff = $this->getDirty();
        $updatedAtColumnName = $this->getUpdatedAtColumn();

        // If the only changed column is the updated_at (touch) just proceed normal
        if (count($diff) == 1 && isset($diff[$updatedAtColumnName])) {
            return $defaultChannels;
        }

        $diffWithoutUpdatedAtAndOrderId = array_filter($diff, function($key) use($updatedAtColumnName) {
            return $key != $updatedAtColumnName && $key != 'order_id';
        }, ARRAY_FILTER_USE_KEY);

        // Only broadcast with real changes (order_id doesn't count)
        if (count($diff) > 1 && count($diffWithoutUpdatedAtAndOrderId) == 0) {
            return [];
        }

        return $defaultChannels;
    }

    public function broadcastWith(): array
    {
        return [
            'model' => $this->with(
                'comments',
                    'comments.user',
                    'comments.likes',
                    'mediaSubscriptions.medium',
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

    public function path()
    {
        return route('kanban.show', $this->id);
    }

    public function kanban()
    {
        return $this->belongsTo(Kanban::class);
    }

    public function comments()
    {
        return $this->hasMany(KanbanItemComment::class);
    }

    public function subscribable()
    {
        return $this->morphTo();
    }

    public function subscriptions()
    {
        return $this->hasMany(KanbanItemSubscription::class);
    }

    public function userSubscriptions()
    {
        return $this->hasMany(KanbanItemSubscription::class)
            ->where('subscribable_type', 'App\User');
    }

    public function groupSubscriptions()
    {
        return $this->hasMany(KanbanItemSubscription::class)
            ->where('subscribable_type', 'App\Group');
    }

    public function organizationSubscriptions()
    {
        return $this->hasMany(KanbanItemSubscription::class)
            ->where('subscribable_type', 'App\Organization');
    }

    public function status()
    {
        return $this->hasOne(KanbanStatus::class);
    }

    public function owner()
    {
        return $this->hasOne('App\User', 'id', 'owner_id');
    }

    public function mediaSubscriptions()
    {
        return $this->morphMany('App\MediumSubscription', 'subscribable');
    }

    public function taskSubscription()
    {
        return $this->morphMany('App\TaskSubscription', 'subscribable');
    }

    public function media()
    {
        return $this->hasManyThrough(
            'App\Medium',
            'App\MediumSubscription',
            'subscribable_id', // Foreign key on medium_subscription table...
            'id', // Foreign key on medium table...
            'id', // Local key on enabling_objectives table...
            'medium_id' // Local key on medium_subscription table...
        )->where('subscribable_type', get_class($this));
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
    public function editors($select = NULL)
    {
        if ($select == NULL){
            return User::whereIn('id', $this->editors_ids);
        } else {
            return User::whereIn('id', $this->editors_ids)->select($select)->get();
        }
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
     * @param  array|string|id $ids
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

    public function isEditable($user = null, $sharing_token = null)
    {
        return $this->kanban->isEditable($user, $sharing_token);
    }
}
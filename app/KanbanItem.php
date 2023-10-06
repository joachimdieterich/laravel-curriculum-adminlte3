<?php

namespace App;

use DateTimeInterface;
use Maize\Markable\Markable;
use Maize\Markable\Models\Like;
use Illuminate\Database\Eloquent\Model;

class KanbanItem extends Model
{
    use Markable;

    protected $guarded = [];

    protected $casts = [
        'created_at' => 'datetime:d.m.Y H:i',
        'locked' => 'boolean',
        'editable' => 'boolean',
        'visibility' => 'boolean',
    ];

    protected $dates = [
        'updated_at',
        'created_at',
        'visible_from',
        'visible_until',
    ];

    protected static $marks = [
        Like::class,
    ];

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
        return $this->belongsTo('App\Kanban', 'kanban_id', 'id');
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
        return $this->hasOne('App\KanbanStatus', 'id', 'kanban_status_id');
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
}

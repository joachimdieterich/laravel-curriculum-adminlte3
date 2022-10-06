<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class KanbanItem extends Model
{
    protected $guarded = [];

    protected $casts = [
        'created_at' => 'datetime:d.m.Y H:i',
    ];

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

    public function isAccessible()
    {
        return $this->kanban->isAccessible();
    }
}

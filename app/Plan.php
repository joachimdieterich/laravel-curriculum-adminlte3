<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $guarded = [];

    protected $casts = [
        'entry_order' => 'array',
        'allow_copy' => 'boolean',
    ];

    protected $attributes = [
        'type_id' => 1,  //= Wochenplan
    ];

    public function path()
    {
        return "/plans/{$this->id}";
    }

    public function owner()
    {
        return $this->hasOne('App\User', 'id', 'owner_id');
    }

    public function entries()
    {
        return $this->hasMany(PlanEntry::class);
    }

    public function taskSubscriptions()
    {
        return $this->morphMany('App\TaskSubscription', 'subscribable');
    }

    public function tasks()
    {
        return $this->hasManyThrough(
            'App\Task',
            'App\TaskSubscription',
            'subscribable_id', // Foreign key on task_subscription table...
            'id', // Foreign key on task table...
            'id', // Local key on plan table...
            'task_id' // Local key on task_subscription table...
        )->where('subscribable_type', get_class($this));
    }

    public function type()
    {
        return $this->hasOne('App\PlanType', 'id', 'type_id');
    }

    public function subscriptions()
    {
        return $this->hasMany(PlanSubscription::class);
    }

    public function mediaSubscriptions()
    {
        return $this->morphMany('App\MediumSubscription', 'subscribable');
    }

    public function userSubscriptions()
    {
        return $this->hasMany(PlanSubscription::class)
            ->where('subscribable_type', 'App\User');
    }

    public function groupSubscriptions()
    {
        return $this->hasMany(PlanSubscription::class)
            ->where('subscribable_type', 'App\Group');
    }

    public function organizationSubscriptions()
    {
        return $this->hasMany(PlanSubscription::class)
            ->where('subscribable_type', 'App\Organization');
    }

    public function isAccessible()
    {
        if (
            auth()->user()->plans->contains('id', $this->id) // user enrolled
            or ($this->subscriptions->where('subscribable_type', "App\Group")->whereIn('subscribable_id', auth()->user()->groups->pluck('id')))->isNotEmpty() //user is enroled in group
            or ($this->subscriptions->where('subscribable_type', "App\Organization")->whereIn('subscribable_id', auth()->user()->current_organization_id))->isNotEmpty() //user is enroled in organization
            or ($this->owner_id == auth()->user()->id)            // or owner
            or is_admin() // or admin
        ) {
            return true;
        } else {
            return false;
        }
    }

    public function isEditable() {
        $user = auth()->user();
        
        return
            is_admin() ||
            $this->owner_id == $user->id ||
            $this->userSubscriptions()->where('subscribable_id', $user->id)->where('editable', 1)->first() ||
            $this->groupSubscriptions()->whereIn('subscribable_id', $user->groups->pluck('id'))->where('editable', 1)->first() ||
            $this->organizationSubscriptions()->whereIn('subscribable_id', $user->organizations->pluck('id'))->where('editable', 1)->first();
    }

    protected static function booted() {
        static::deleting(function(Plan $plan) { // before delete() method call this
            $plan->subscriptions()->delete();
            //? if media-subscriptions can be added in the future, they need to be deleted too
            $plan->entries->each->delete();
        });
    }
}

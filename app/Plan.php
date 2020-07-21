<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $guarded = [];
    
    public function path()
    {
        return "/plans/{$this->id}";
    }
    
    public function owner()
    {
        return $this->hasOne('App\User', 'id', 'owner_id');   
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
   
}

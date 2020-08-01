<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Kanban extends Model
{
    
    protected $guarded = [];
    
    public function path()
    {
        return route('kanbans.show', $this->id);
    }
    
    public function items()
    {
        return $this->hasMany('App\KanbanItem', 'kanban_id', 'id')->orderBy('order_id');
    }
    
    public function statuses()
    {
        return $this->hasMany('App\KanbanStatus', 'kanban_id', 'id')->orderBy('order_id');
    }
    
    public function subscriptions()
    {
        return $this->hasMany(KanbanSubscription::class);
    }
    
    public function userSubscriptions()
    {
        return $this->hasMany(KanbanSubscription::class)
                    ->where('subscribable_type', 'App\User'); 
    }
    
    public function groupSubscriptions()
    {
        return $this->hasMany(KanbanSubscription::class)
                    ->where('subscribable_type', 'App\Group'); 
    }
    
    public function organizationSubscriptions()
    {
        return $this->hasMany(KanbanSubscription::class)
                    ->where('subscribable_type', 'App\Organization'); 
    }
    
    public function owner()
    {
        return $this->hasOne('App\User', 'id', 'owner_id');   
    }
}

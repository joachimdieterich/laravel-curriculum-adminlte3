<?php

namespace App;

use App\TaskSubscription;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $guarded = [];
    
    public function path()
    {
        return "/tasks/{$this->id}";
    }
    
    public function owner(){
        return $this->belongsTo(User::class);
    }
    
    public function subscriptions()
    {
        return $this->hasMany(TaskSubscription::class);
    }
    
    public function subscribe($model)
    {
        
        $subscription =  TaskSubscription::firstOrNew([
			"task_id" =>  $this->id,
			"subscribable_type"=> get_class($model),
			"subscribable_id"=> $model->id,
			"owner_id"=> auth()->user()->id,	
	]);
        $subscription->save();
        return $subscription;
    }
    
    public function enablingObjectiveSubscriptions()
    {
        return $this->morphMany('App\EnablingObjectiveSubscriptions', 'subscribable');
    }
    
    public function terminalObjectiveSubscriptions()
    {
        return $this->morphMany('App\TerminalObjectiveSubscriptions', 'subscribable');
    }
   
    public function mediaSubscriptions()
    {
        return $this->morphMany('App\MediumSubscription', 'subscribable');
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
    
    public function contents()
    {
        return $this->hasManyThrough(
            'App\Content',
            'App\ContentSubscription',
            'subscribable_id', // Foreign key on content_subscription table...
            'id', // Foreign key on content table...
            'id', // Local key on logbookEntry table...
            'content_id' // Local key on content_subscription table...
        )->where('subscribable_type', get_class($this)); 
    }
    
    public function contentSubscriptions()
    {
        return $this->morphMany('App\ContentSubscription', 'subscribable');
    }
}

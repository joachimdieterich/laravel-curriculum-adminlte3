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
    
   
}

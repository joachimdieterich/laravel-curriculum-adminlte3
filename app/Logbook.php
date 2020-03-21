<?php

namespace App;

use App\LogbookSubscription;
use Illuminate\Database\Eloquent\Model;

class Logbook extends Model
{
    protected $guarded = [];
    
    public function path()
    {
        return "/logbooks/{$this->id}";
    }
    
    public function entries()
    {
        return $this->hasMany('App\LogbookEntry')->orderBy('begin', 'DESC');
    }
    
    public function subscriptions()
    {
        return $this->hasMany(LogbookSubscription::class);
    }
    
    public function owner(){
        return $this->belongsTo(User::class);
    }
    
    public function subscribe($model)
    {
        
        $subscription =  LogbookSubscription::firstOrNew([
			"logbook_id" =>  $this->id,
			"subscribable_type"=> get_class($model),
			"subscribable_id"=> $model->id,
			"owner_id"=> auth()->user()->id,	
	]);
        $subscription->save();
        return $subscription;
    }
    
}

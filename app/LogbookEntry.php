<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LogbookEntry extends Model
{
    protected $guarded = [];
    
    public function path()
    {
        return "/logbookEntries/{$this->id}";
    }
    
    public function logbook(){
        return $this->belongsTo('App\Logbook')->withTimestamps();
    }
    
    public function contentSubscriptions()
    {
        return $this->morphMany('App\ContentSubscription', 'subscribable');
    }
    
    public function enablingObjectiveSubscriptions()
    {
        return $this->morphMany('App\enablingObjectiveSubscriptions', 'subscribable');
    }
    
    public function terminalObjectiveSubscriptions()
    {
        return $this->morphMany('App\terminalObjectiveSubscriptions', 'subscribable');
    }
    
    public function taskSubscription()
    {
        return $this->morphMany('App\TaskSubscription', 'subscribable');
    }
}

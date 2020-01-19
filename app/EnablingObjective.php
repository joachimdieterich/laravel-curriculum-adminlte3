<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EnablingObjective extends Model
{
    protected $fillable = [ 'title', 
                            'description',
                            'order_id', 
                            'time_approach',
                            'curriculum_id',
                            'terminal_objective_id',
                            'level_id',
                          ];
    protected $with = ['terminalObjective', 'level'];
    
    public function path(){
        return "/curricula/{$this->curriculum_id}";
    }
    
    public function type()
    {
        return $this->belongsTo('App\TerminalObjective', 'id', 'terminal_objective_id');
    }
     
    public function terminalObjective()
    {
        return $this->belongsTo('\App\TerminalObjective', 'terminal_objective_id', 'id');
    }
    
    public function contentSubscriptions()
    {
        return $this->morphMany('App\ContentSubscription', 'subscribable');
    }
    
    public function curriculum()
    {
        return $this->belongsTo('\App\Curriculum', 'curriculum_id', 'id');
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
    
    public function references()
    {
        return $this->hasManyThrough(
            'App\Reference',
            'App\ReferenceSubscription',
            'referenceable_id', // Foreign key on reference_subscription table...
            'id', // Foreign key on reference table...
            'id', // Local key on enabling_objectives table...
            'reference_id' // Local key on reference_subscription table...
        )->where('referenceable_type', get_class($this)); 
    }
   
    public function referenceSubscriptions()
    {
        return $this->morphMany('App\ReferenceSubscription', 'referenceable');
    }
    
    public function quoteSubscriptions()
    {
        return $this->morphMany('App\QuoteSubscription', 'quotable');
    }

    public function achievements()
    {
        return $this->morphMany('App\Achievement', 'referenceable');
    }
    
    public function level() 
    {
        return $this->hasOne('App\Level', 'id', 'level_id');
    }
    
}

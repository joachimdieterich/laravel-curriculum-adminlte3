<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TerminalObjectiveSubscriptions extends Model
{
    protected $guarded = [];
    
    /**
     * Get the subscriber model.
     */
    public function subscribable()
    {
        return $this->morphTo();
    }
    
    public function terminalObjective(){
        return $this->belongsTo('App\TerminalObjective', 'terminal_objective_id', 'id');
    }
    
    public function status()
    {
        return $this->hasOne('App\Status', 'status_id', 'status_id');
    }
}

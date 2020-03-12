<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\ModelStatus\HasStatuses;

class TaskSubscription extends Model
{
    protected $guarded = [];
    
    use HasStatuses;
    
    
    public function subscribable()
    {
        return $this->morphTo();
    }
    
    public function task(){
        return $this->belongsTo('App\Task', 'task_id', 'id');
    }
    
    public function complete()
    {
        $this->update(['completion_date' => date("Y-m-d H:i:s")]);

       // $this->recordActivity('completed_task');
    }

    /**
     * Mark the task as incomplete.
     */
    public function incomplete()
    {
        $this->update(['completion_date' => null]);

        // $this->recordActivity('incompleted_task');
    }
    
    public function owner()
    {
        return $this->hasOne('App\User', 'id', 'owner_id');   
    }
    
}

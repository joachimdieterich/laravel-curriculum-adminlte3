<?php

namespace App;

use App\Http\Controllers\LogController;
use Illuminate\Database\Eloquent\Model;

class TaskSubscription extends Model
{
    protected $guarded = [];

    public function subscribable()
    {
        return $this->morphTo();
    }

    public function task()
    {
        return $this->belongsTo('App\Task', 'task_id', 'id');
    }

    public function complete()
    {
        $this->update(['completion_date' => date('Y-m-d H:i:s')]);
        LogController::set(get_class($this).'@'.__FUNCTION__);
    }

    /**
     * Mark the task as incomplete.
     */
    public function incomplete()
    {
        $this->update(['completion_date' => null]);
        LogController::set(get_class($this).'@'.__FUNCTION__);
        // $this->recordActivity('incompleted_task');
    }

    public function owner()
    {
        return $this->hasOne('App\User', 'id', 'owner_id');
    }

    public function isAccessible()
    {
        return $this->task->isAccessible();
    }
}

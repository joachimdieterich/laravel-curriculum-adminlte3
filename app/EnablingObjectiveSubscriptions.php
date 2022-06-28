<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EnablingObjectiveSubscriptions extends Model
{
    protected $guarded = [];

    /**
     * Get the subscriber model.
     */
    public function subscribable()
    {
        return $this->morphTo();
    }

    public function enablingObjective()
    {
        return $this->belongsTo('App\EnablingObjective', 'enabling_objective_id', 'id');
    }
}

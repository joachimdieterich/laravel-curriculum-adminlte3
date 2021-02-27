<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PlanSubscription extends Model
{
    protected $fillable = [
        'subscribable_type',
        'subscribable_id',
        'plan_id',
        'editable',
        'owner_id'];
    /**
     * Get the subscriber model.
     */
    public function subscribable()
    {
        return $this->morphTo();
    }

    public function plan(){
        return $this->belongsTo('App\Plan', 'plan_id', 'id');
    }
}

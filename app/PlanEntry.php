<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;

class PlanEntry extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'title',
        'description',
        'plan_id',
        'order_id',
        'css_icon',
        'color',
        'medium_id',
        'owner_id'

    ];

    protected $dates = [
        'updated_at',
        'created_at',
    ];

    /**
     * Prepare a date for array / JSON serialization.
     *
     * @param  \DateTimeInterface  $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function plan()
    {
        return $this->belongsTo('App\Plan', 'plan_id', 'id');
    }

    public function mediaSubscriptions()
    {
        return $this->morphMany('App\MediumSubscription', 'subscribable');
    }

    public function medium()
    {
        return $this->hasOne('App\Medium', 'id', 'medium_id');
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

    public function enablingObjectiveSubscriptions()
    {
        return $this->morphMany('App\EnablingObjectiveSubscriptions', 'subscribable');
    }

    public function terminalObjectiveSubscriptions()
    {
        return $this->morphMany('App\TerminalObjectiveSubscriptions', 'subscribable');
    }
    
    public function trainingSubscriptions()
    {
        return $this->morphMany('App\TrainingSubscription', 'subscribable');
    }

    public function trainings()
    {
        return $this->hasManyThrough(
            'App\Training',
            'App\TrainingSubscription',
            'subscribable_id',
            'id',
            'id',
            'training_id'
        )->where('subscribable_type', get_class($this));
    }

    public function isAccessible()
    {
        return $this->plan->isAccessible();
    }

}

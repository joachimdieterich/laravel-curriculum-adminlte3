<?php

namespace App;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnablingObjective extends Model
{
    use HasFactory;

    protected $fillable = ['title',
        'description',
        'order_id',
        'time_approach',
        'curriculum_id',
        'terminal_objective_id',
        'level_id',
        'visibility',
    ];

    protected $casts = [
        'visibility' => 'boolean',
        'referencing_curriculum_id' => 'object',
    ];

    protected $dates = [
        'updated_at',
        'created_at',
    ];

    protected $with = ['terminalObjective', 'level'];

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

    public function path()
    {
        return "/curricula/{$this->curriculum_id}";
    }

    public function type()
    {
        return $this->belongsTo('App\TerminalObjective', 'id', 'terminal_objective_id');
    }

    public function terminalObjective()
    {
        return $this->belongsTo('App\TerminalObjective', 'terminal_objective_id', 'id');
    }

    public function contents()
    {
        return $this->hasManyThrough(
            'App\Content',
            'App\ContentSubscription',
            'subscribable_id', // Foreign key on content_subscription table...
            'id', // Foreign key on content table...
            'id', // Local key on enablin objectives table...
            'content_id' // Local key on content_subscription table...
        )->where('subscribable_type', get_class($this));
    }

    public function contentSubscriptions()
    {
        return $this->morphMany('App\ContentSubscription', 'subscribable');
    }

    public function curriculum()
    {
        return $this->belongsTo('App\Curriculum', 'curriculum_id', 'id');
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

    public function repositorySubscriptions()
    {
        return $this->morphMany('App\RepositorySubscription', 'subscribable');
    }

    public function subscriptions()
    {
        return $this->hasMany(EnablingObjectiveSubscriptions::class);
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

    public function predecessors()
    {
        return $this->morphMany('App\Prerequisites', 'successor');
    }

    public function successors()
    {
        return $this->morphMany('App\Prerequisites', 'predecessor');
    }

    public function variants()
    {
        return $this->morphMany('App\Variant', 'referenceable');
    }

    public function isAccessible()
    {
        return $this->curriculum->isAccessible();
    }
}

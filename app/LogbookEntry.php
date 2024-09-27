<?php

namespace App;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

class LogbookEntry extends Model
{
    protected $guarded = [];

    protected $casts = [
        'updated_at' => 'datetime',
        'created_at'  => 'datetime',
        'begin'  => 'datetime',
        'end'  => 'datetime',
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

    public function path()
    {
        return "/logbookEntries/{$this->id}";
    }

    public function logbook()
    {
        return $this->belongsTo('App\Logbook');
        //return $this->belongsTo('App\Logbook')->withTimestamps(); --> has to be without timestamps to get isAccessible working
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function absences()
    {
        return $this->morphMany('App\Absence', 'referenceable');
    }

    public function contents()
    {
        return $this->hasManyThrough(
            'App\Content',
            'App\ContentSubscription',
            'subscribable_id', // Foreign key on content_subscription table...
            'id', // Foreign key on content table...
            'id', // Local key on logbookEntry table...
            'content_id' // Local key on content_subscription table...
        )->where('subscribable_type', get_class($this));
    }

    public function contentSubscriptions()
    {
        return $this->morphMany('App\ContentSubscription', 'subscribable');
    }

    public function enablingObjectiveSubscriptions()
    {
        return $this->morphMany('App\EnablingObjectiveSubscriptions', 'subscribable');
    }

    public function terminalObjectiveSubscriptions()
    {
        return $this->morphMany('App\TerminalObjectiveSubscriptions', 'subscribable');
    }

    public function taskSubscription()
    {
        return $this->morphMany('App\TaskSubscription', 'subscribable');
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

    public function owner()
    {
        return $this->belongsTo(User::class);
    }

    public function isAccessible()
    {
        return $this->logbook->isAccessible();
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Mews\Purifier\Casts\CleanHtml;
use DateTimeInterface;

class Training extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'title',
        'description',
        'begin',
        'end',
        'order_id',
        'subscribable_type',
        'subscribable_id',
        'owner_id',
    ];

    protected $casts = [
        'description' => CleanHtml::class, // cleans both when getting and setting the value
        'begin' => 'datetime',
        'end' => 'datetime',
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
        return "/trainings/{$this->id}";
    }

    public function subscriptions()
    {
        return $this->hasMany(TrainingSubscription::class);
    }

    public function exercises()
    {
        return $this->hasMany(Exercise::class);
    }

    public function isAccessible()
    {
        if (
            $this->subscriptions->first()->subscribable->plan->isAccessible() //user has access throught a plan
            or ($this->owner_id == auth()->user()->id)            // or owner
            or is_admin() // or admin
        ) {
            return true;
        } else {
            return false;
        }
    }

    protected static function booted() {
        static::deleting(function(Training $training) { // before delete() method call this
            $training->exercises->each->delete();
            $training->subscriptions()->delete();
        });
    }
}

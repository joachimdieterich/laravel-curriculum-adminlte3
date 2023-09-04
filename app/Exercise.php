<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;

class Exercise extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'training_id',
        'title',
        'description',
        'recommended_iterations',
        'owner_id'
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

    public function training()
    {
        return $this->belongsTo('App\Training');
    }

    public function dones()
    {
        return $this->hasMany(ExerciseDone::class);
    }

    protected static function booted () {
        static::deleting(function(Exercise $exercise) { // before delete() method call this
            $exercise->dones()->delete();
        });
    }

    public function isAccessible()
    {
        if (
            //auth()->user()->trainings->contains('id', $this->id) // user enrolled
            $this->owner_id == auth()->user()->id            // or owner
            or is_admin() // or admin
        ) {
            return true;
        } else {
            return false;
        }
    }


}

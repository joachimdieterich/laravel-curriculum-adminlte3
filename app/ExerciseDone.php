<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;

class ExerciseDone extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'exercise_id',
        'iterations',
        'user_id',
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

    public function exercise()
    {
        return $this->belongsTo('App\Exercise');
    }

    public function user()
    {
        return $this->hasOne('App\User', 'id', 'user_id');
    }

    public function owner()
    {
        return $this->hasOne('App\User', 'id', 'owner_id');
    }
}

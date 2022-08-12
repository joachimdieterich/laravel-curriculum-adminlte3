<?php

namespace App;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

class Absence extends Model
{
    protected $guarded = [];

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

    public function owner()
    {
        return $this->hasOne('App\User', 'id', 'owner_id');
    }

    public function absent_user()
    {
        return $this->hasOne('App\User', 'id', 'absent_user_id');
    }

    public function referenceable()
    {
        return $this->morphTo();
    }

    public function isAccessible()
    {
        if (
            $this->referenceable->isAccessible()
            or ($this->owner_id == auth()->user()->id)            // or owner
            or is_admin() // or admin
        ) {
            return true;
        } else {
            return false;
        }
    }
}

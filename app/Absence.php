<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Absence extends Model
{
    protected $guarded = [];

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

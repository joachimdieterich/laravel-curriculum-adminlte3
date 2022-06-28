<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    public function objectives()
    {
        $this->belongsToMany('App\EnablingObjective');
    }
}

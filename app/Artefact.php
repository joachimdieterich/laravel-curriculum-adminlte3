<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Artefact extends Model
{
    protected $guarded = [];


    public function medium()
    {
        return $this->hasOne('App\Medium', 'id', 'medium_id');
    }
}

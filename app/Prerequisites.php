<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Prerequisites extends Model
{

    protected $guarded = [];

    public function predecessor() {
        return $this->morphTo();
    }

    public function successor() {
        return $this->morphTo();
    }

    public function owner()
    {
        return $this->hasOne('App\User', 'id', 'owner_id');
    }
}

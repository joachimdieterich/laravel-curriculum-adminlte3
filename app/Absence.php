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
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Period extends Model
{
    public function organization()
    {
        return $this->hasOne('App\Organization', 'id', 'organization_id');
    }
    
    public function owner()
    {
        return $this->hasOne('App\User', 'id', 'owner_id');
    }
}

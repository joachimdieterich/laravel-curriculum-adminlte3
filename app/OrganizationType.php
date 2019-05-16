<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrganizationType extends Model
{
    public function state()
    {
        return $this->hasOne('App\State', 'code', 'state_id');
    }
    
    public function country()
    {
        return $this->hasOne('App\Country', 'alpha2', 'country_id');   
    }
}

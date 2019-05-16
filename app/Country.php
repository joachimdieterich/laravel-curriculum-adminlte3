<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\State;

class Country extends Model
{
   public function states()
   {
        return $this->hasMany(State::class, 'country', 'alpha2');
   }
    
}

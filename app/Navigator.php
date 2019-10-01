<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Navigator extends Model
{
    public function path()
    {
        return "/navigators/{$this->id}";
    }
    
    public function views() 
    {
        return $this->hasMany('App\NavigatorView', 'navigator_id', 'id');
    }
    
}

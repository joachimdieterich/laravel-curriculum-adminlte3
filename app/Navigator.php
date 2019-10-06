<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Navigator extends Model
{
    protected $guarded = [];
    
    public function path()
    {
        return "/navigators/{$this->id}";
    }
    
    public function views() 
    {
        return $this->hasMany('App\NavigatorView', 'navigator_id', 'id');
    }
    
    public function organization()
    {
        return $this->belongsTo('App\Organization');
    }
    
}

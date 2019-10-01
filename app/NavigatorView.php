<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NavigatorView extends Model
{
    public function path()
    {
        return "/navigators/{$this->navigator_id}/{$this->id}";
    }
    
    public function items()
    {
        return $this->hasMany('App\NavigatorItem', 'navigator_view_id', 'id');
    }
    
    public function navigator()
    {
        return $this->belongsTo('App\Navigator', 'id', 'navigator_id');
    }
}

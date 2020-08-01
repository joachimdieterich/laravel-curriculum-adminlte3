<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class KanbanStatus extends Model
{
    protected $guarded = [];
    
    public function items()
    {
        return $this->hasMany('App\KanbanItem')->orderBy('order_id');
    }
    
    
    public function owner()
    {
        return $this->hasOne('App\User', 'id', 'owner_id');   
    }
}

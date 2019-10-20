<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    protected $guarded = [];
     
    public function path()
    {
        return "/certificates/{$this->id}";
    }
    
    public function curriculum()
    {
        return $this->belongsTo('App\Curriculum');   
    }
    
    public function organization()
    {
        return $this->belongsTo('App\Organization');   
    }
    
    public function owner()
    {
        return $this->hasOne('App\User', 'id', 'owner_id');   
    }
}

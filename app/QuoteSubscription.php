<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuoteSubscription extends Model
{
    protected $guarded = [];
    
    protected $casts = ['quote_id' => 'string']; //important to get id as unique string
    
     public function quote() 
    {
        return $this->hasOne('App\Quote', 'id', 'quote_id');
    }
    
    public function quotable()
    {
        return $this->morphTo();
    }
    
    public function siblings()
    {  
        return $this->hasMany('App\QuoteSubscription', 'quote_id', 'quote_id')->with(['quote', 'quotable.curriculum.organizationType']);
       
    }
    
}

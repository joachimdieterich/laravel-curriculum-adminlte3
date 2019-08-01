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
    
}

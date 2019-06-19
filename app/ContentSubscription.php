<?php

namespace App;


use Illuminate\Database\Eloquent\Model;

class ContentSubscription extends Model
{
    protected $guarded = [];
    
    public function curriculum()
    {
        return $this->belongsTo(Content::class);
    }
    
    public function status()
    {
        return $this->hasOne('App\Status', 'status_id', 'status_id');
    }
}

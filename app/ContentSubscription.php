<?php

namespace App;

use App\Content;
use Illuminate\Database\Eloquent\Model;

class ContentSubscription extends Model
{
    protected $guarded = [];
    
    public function content()
    {
        return $this->belongsTo(Content::class);
    }
    
    public function status()
    {
        return $this->hasOne('App\Status', 'status_id', 'status_id');
    }
}

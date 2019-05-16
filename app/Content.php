<?php

namespace App;

use App\ContentSubscription;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    protected $guarded = [];

    public function path()
    {
        return "/contents/{$this->id}";
    }
    
    public function owner(){
        return $this->belongsTo(User::class);
    }
    
    public function subscriptions()
    {
        return $this->hasMany(ContentSubscription::class);
    }
}

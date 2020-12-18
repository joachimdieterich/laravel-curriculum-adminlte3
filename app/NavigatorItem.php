<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NavigatorItem extends Model
{
    protected $guarded = [];

    public function navigatorView()
    {
        return $this->belongsTo('App\NavigatorView');
    }

    public function referenceable()
    {
        return $this->morphTo();
    }

    public function medium()
    {
        return $this->hasOneThrough(
            'App\Medium',
            'App\MediumSubscription',
            'subscribable_id', // Foreign key on medium_subscription table...
            'id', // Foreign key on medium table...
            'id', // Local key on navigator_items table...
            'medium_id' // Local key on medium_subscription table...
        )->where('subscribable_type', get_class($this));
    }
}

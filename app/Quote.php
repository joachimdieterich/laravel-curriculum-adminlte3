<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    protected $guarded = [];

    protected $casts = ['id' => 'string']; //important to get id as unique string

    /**
     * Get the owning commentable model.
     */
    public function content()
    {
        return $this->belongsTo('App\Content', 'referenceable_id', 'id');
    }

    public function subscriptions()
    {
        return $this->morphMany('App\QuoteSubscription', 'quotable');
    }
}

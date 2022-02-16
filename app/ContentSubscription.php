<?php

namespace App;


use Illuminate\Database\Eloquent\Model;

class ContentSubscription extends Model
{
    protected $guarded = [];

//    public function curriculum()
//    {
//        return $this->belongsTo(Content::class);
//    }

    /**
     * Get the subscriber model.
     */
    public function subscribable()
    {
        return $this->morphTo();
    }

    public function content()
    {
        return $this->belongsTo('App\Content', 'content_id', 'id');
    }

    public function isAccessible()
    {
        return $this->subscribable->isAccessible();
    }

}

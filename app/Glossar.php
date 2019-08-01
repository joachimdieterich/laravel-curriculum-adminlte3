<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Glossar extends Model
{
    protected $guarded = [];
     
    public function contents()
    {
        return $this->hasManyThrough(
            'App\Content',
            'App\ContentSubscription',
            'subscribable_id', // Foreign key on content_subscription table...
            'id', // Foreign key on content table...
            'id', // Local key on curriculum table...
            'content_id' // Local key on content_subscription table...
        )->where('subscribable_type', get_class($this)); 
    }
}

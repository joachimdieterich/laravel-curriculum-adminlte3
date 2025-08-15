<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $table = 'curriculum_subscriptions';

    /*public function group()
    {
        return $this->belongsTo('App\Group', 'group_id', 'id');
    }*/

    public function curriculum()
    {
        return $this->belongsTo('App\Curriculum', 'curriculum_id', 'id');
    }

    public function logbookSubscription()
    {
        return $this->morphMany('App\LogbookSubscription', 'subscribable');
    }

    public function isAccessible()
    {
        if (
            $this->curriculum->isAccessible()
        ) {
            return true;
        } else {
            return false;
        }
    }
}

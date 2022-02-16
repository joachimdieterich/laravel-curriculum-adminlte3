<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $table = 'curriculum_group';

    public function group()
    {
        return $this->belongsTo('App\Group', 'group_id', 'id');
    }

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
            auth()->user()->curricula()->contains('id', $this->curriculum_id) // user enrolled
            or is_admin()
        ) {
            return true;
        } else {
            return false;
        }
    }
}

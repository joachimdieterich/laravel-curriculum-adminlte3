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
}

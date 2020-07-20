<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CurriculumType extends Model
{
    public function curricula()
    {
        return $this->belongsTo('App\Curriculum', 'type_id', 'id');
    }
}

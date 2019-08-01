<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ObjectiveType extends Model
{
  
    public function objectives()
    {
        return $this->belongsTo('App\TerminalObjective', 'objective_type_id', 'id');
    }
}
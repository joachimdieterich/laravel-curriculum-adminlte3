<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ObjectiveType extends Model
{
    protected $guarded = [];

    public function path()
    {
        return route('objectiveTypes.show', $this->id);
    }

    public function objectives()
    {
        return $this->belongsTo('App\TerminalObjective', 'objective_type_id', 'id');
    }
}

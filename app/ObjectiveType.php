<?php

namespace App;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

class ObjectiveType extends Model
{
    protected $guarded = [];

    protected $casts = [
        'updated_at' => 'datetime',
        'created_at'  => 'datetime',
    ];

    /**
     * Prepare a date for array / JSON serialization.
     *
     * @param  \DateTimeInterface  $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }


    public function path()
    {
        return route('objectiveTypes.show', $this->id);
    }

    public function terminalObjectives()
    {
        return $this->hasMany('App\TerminalObjective', 'objective_type_id', 'id');
    }
}

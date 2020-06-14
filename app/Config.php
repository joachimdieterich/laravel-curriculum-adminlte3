<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    protected $fillable = [ 
        'key', 
        'value',
        'referenceable_type', 
        'referenceable_id',
        'data_type',
    ];
    public function path()
    {
        return route('configs.show', $this->id);
    }
}

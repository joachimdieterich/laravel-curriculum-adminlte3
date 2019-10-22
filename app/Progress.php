<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Progress extends Model
{
    protected $guarded = [''];
    public function referenceable()
    {
        return $this->morphTo();
    }
    
    public function associable()
    {
        return $this->morphTo();
    }
}

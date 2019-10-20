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
    
    public function connectable()
    {
        return $this->morphTo();
    }
}

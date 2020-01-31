<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Logbook extends Model
{
    protected $guarded = [];
    
    public function path()
    {
        return "/logbooks/{$this->id}";
    }
    
    public function entries()
    {
        return $this->hasMany('App\LogbookEntry')->orderBy('begin', 'DESC');
    }
}

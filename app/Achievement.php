<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
class Achievement extends Model
{
    protected $guarded = [''];

    protected $cast = ['status' => 'string']; //important to get id as unique string

    public function notes()
    {
        return $this->morphMany('App\Note', 'notable');
    }

    public function user()
    {
        return $this->hasOne('App\User', 'id', 'user_id');
    }

    public function owner()
    {
        return $this->hasOne('App\User', 'id', 'owner_id');
    }

}

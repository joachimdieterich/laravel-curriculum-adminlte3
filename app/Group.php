<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    
    public function enrol(User $user)
    {
        return $this->users()->attach($user);
    }
    
    public function users(){
        return $this->belongsToMany(User::class, 'group_user')->withTimestamps();
    }
}

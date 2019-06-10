<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Curriculum extends Model
{
    protected $guarded = [];
     
    public function path()
    {
        return "/admin/curricula/{$this->id}";
    }
    
    public function enrol(Group $group)
    {
        return $this->groups()->attach($group);
    }
    
    public function expel(Group $group)
    {
        return $this->groups()->detach($group);
    }
    
    public function state()
    {
        return $this->hasOne('App\State', 'code', 'state_id');
    }
    
    public function country()
    {
        return $this->hasOne('App\Country', 'alpha2', 'country_id');   
    }
    
    public function grade()
    {
        return $this->hasOne('App\Grade', 'id', 'grade_id');   
    }
    
    public function subject()
    {
        return $this->hasOne('App\Subject', 'external_id', 'subject_id');   
    }
    
    public function owner()
    {
        return $this->hasOne('App\User', 'id', 'owner_id');   
    }
    
    public function groups()
    {
        return $this->belongsToMany('App\Group', 'group_user');
    }
}

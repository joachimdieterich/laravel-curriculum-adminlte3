<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $guarded = [];
    
    public function path()
    {
        return "/admin/groups/{$this->id}";
    }
    
    public function enrol(User $user)
    {
        return $this->users()->attach($user);
    }
    
    public function users(){
        return $this->belongsToMany(User::class, 'group_user')->withTimestamps();
    }
    
    public function curricula(){
        return $this->belongsToMany(Curriculum::class, 'curriculum_group');
    }
    
    public function grade()
    {
        return $this->hasOne('App\Grade', 'id', 'grade_id');
    }
    
    public function period()
    {
        return $this->hasOne('App\Period', 'id', 'period_id');
    }
    
    public function organization()
    {
        return $this->hasOne('App\Organization', 'id', 'organization_id');
    }
    
    public function owner()
    {
        return $this->hasOne('App\User', 'id', 'owner_id');
    }
}

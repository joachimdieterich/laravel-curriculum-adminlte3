<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Curriculum extends Model
{
    protected $guarded = [];
     
    public function path()
    {
        return "/curricula/{$this->id}";
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
    public function organizationType()
    {
        return $this->hasOne('App\organizationType', 'id', 'organization_type_id');   
    }
    
    public function subject()
    {
        //return $this->hasOne('App\Subject', 'external_id', 'subject_id');   
        return $this->hasOne('App\Subject', 'id', 'subject_id');   
    }
    
    public function owner()
    {
        return $this->hasOne('App\User', 'id', 'owner_id');   
    }
    
    public function groups()
    {
        return $this->belongsToMany('App\Group', 'curriculum_group')->withTimestamps();
    }
    
    public function terminalObjectives()
    {
        return $this->hasMany('App\TerminalObjective', 'curriculum_id', 'id');
    }
    
    public function contentSubscriptions()
    {
        return $this->morphMany('App\ContentSubscription', 'subscribable');
    }
    
    public function contents()
    {
        return $this->hasManyThrough(
            'App\Content',
            'App\ContentSubscription',
            'subscribable_id', // Foreign key on content_subscription table...
            'id', // Foreign key on content table...
            'id', // Local key on curriculum table...
            'content_id' // Local key on content_subscription table...
        )->where('subscribable_type', get_class($this)); 
    }
   
    public function courses(){
        return $this->hasMany('App\Course', 'curriculum_id', 'id');
    }
    
    public function media()
    {
        return $this->hasManyThrough(
            'App\Medium',
            'App\MediumSubscription',
            'subscribable_id', // Foreign key on medium_subscription table...
            'id', // Foreign key on medium table...
            'id', // Local key on curriculum table...
            'medium_id' // Local key on medium_subscription table...
        )->where('subscribable_type', get_class($this)); 
    }
    
    public function glossar()
    {
        return $this->hasOne('App\Glossar', 'subscribable_id', 'id')->where('subscribable_type', get_class($this)); 
    }
    
    public function navigator_item()
    {
        return $this->morphOne('App\NavigatorItem', 'referenceable');
    }
}

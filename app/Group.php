<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 *   @OA\Schema(  
 *      required={"id", "title", "grade_id", "period_id", "organization_id"},
 *      @OA\Xml(name="Group"),
 *      
 *      @OA\Property( property="id", type="integer"),
 *      @OA\Property( property="title", type="string"),
 *      @OA\Property( property="grade_id", type="integer"),
 *      @OA\Property( property="period_id", type="integer"),
 *      @OA\Property( property="organization_id", type="integer"),
 *      @OA\Property( property="created_at", type="string"),
 *      @OA\Property( property="updated_at", type="string")
 *   ),
 * 
 */
class Group extends Model
{
    protected $guarded = [];
    
    public function path()
    {
        return "/groups/{$this->id}";
    }
    
    public function enrol(User $user)
    {
        return $this->users()->attach($user);
    }
    
    public function users(){
        return $this->belongsToMany(User::class, 'group_user')->withTimestamps();
    }
    
    public function curricula(){
        return $this->belongsToMany(Curriculum::class, 'curriculum_group')->withTimestamps();
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
    
    public function courses()
    {
         return $this->hasMany('App\GroupUser', 'group_id', 'id');
    }
    
}

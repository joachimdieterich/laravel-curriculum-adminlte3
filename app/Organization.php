<?php

namespace App;

use App\OrganizationRoleUser;
use Illuminate\Database\Eloquent\Model;
/**
 *   @OA\Schema(  
 *      required={"id", "title", "state_id", "country_id", "organization_type_id", "status_id"},
 *      @OA\Xml(name="Organization"),
 *      
 *      @OA\Property( property="id", type="integer"),
 *      @OA\Property( property="common_name", type="string"),
 *      @OA\Property( property="title", type="string"),
 *      @OA\Property( property="organization_type_id", type="string"),
 *      @OA\Property( property="state_id", type="string"),
 *      @OA\Property( property="country_id", type="string"),
 *      @OA\Property( property="status_id", type="integer"),
 * 
 *      @OA\Property( property="description", type="string"),
 *      @OA\Property( property="street", type="string"),
 *      @OA\Property( property="city", type="string"),
 *      @OA\Property( property="postcode", type="string"),
 *      @OA\Property( property="phone", type="string"),
 *      @OA\Property( property="email", type="string"),
 *      @OA\Property( property="created_at", type="string"),
 *      @OA\Property( property="updated_at", type="string")
 *   ),
 * 
 */
class Organization extends Model
{
    protected $guarded = [];
    
    protected $attributes = [
        'state_id' => 'DE-RP',
        'country_id' => 'DE',
        'organization_type_id' => 1,
        'status_id' => 1,
    ];
    
    
    public function path()
    {
        return "/organizations/{$this->id}";
    }
    
    public function roles()
    {
         return $this->belongsToMany(Role::class, 'organization_role_users')
                ->withPivot(['user_id', 'role_id', 'organization_id']);
    }
    
    // and for all 3 Organization, User, Role this relation:
    public function organizationRolesUsers()
    {
      return $this->hasMany(OrganizationRoleUser::class)
              ->role('Admin');
    }
    
    public function state()
    {
        return $this->hasOne('App\State', 'code', 'state_id');
    }
    
    public function country()
    {
        return $this->hasOne('App\Country', 'alpha2', 'country_id');   
    }
    
    public function type()
    {
        return $this->hasOne('App\OrganizationType', 'external_id', 'organization_type_id');
    }
    
    public function status()
    {
        return $this->hasOne('App\Status', 'status_id', 'status_id');
    }
    
    public function navigators()
    {
        return $this->hasMany('App\Navigator');
    }

}

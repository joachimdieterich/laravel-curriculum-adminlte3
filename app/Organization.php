<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\OrganizationRoleUser;

class Organization extends Model
{
    protected $guarded = [];
    
    
    public function path()
    {
        return "/admin/organizations/{$this->id}";
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
}

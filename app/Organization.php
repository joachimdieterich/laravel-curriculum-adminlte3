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
}

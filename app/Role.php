<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
/**
 *   @OA\Schema(  
 *      required={"id", "title"},
 *      @OA\Xml(name="Role"),
 *      
 *      @OA\Property( property="id", type="integer"),
 *      @OA\Property( property="title", type="string"),
 *      @OA\Property( property="created_at", type="string"),
 *      @OA\Property( property="updated_at", type="string"),
 *      @OA\Property( property="deleted_at", type="string"),
 *   ),
 * 
 */
class Role extends Model
{
    use SoftDeletes;

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'title',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }
    
    public function organizations()
    {
        return $this->belongsToMany(Organization::class, 'organization_role_users')
            ->withPivot(['user_id', 'role_id', 'organization_id']);
    }
    
     // and for all 3 Organization, User, Role this relation:
    public function organizationRolesUsers()
    {
      return $this->hasMany(OrganizationRoleUser::class);
    }
}

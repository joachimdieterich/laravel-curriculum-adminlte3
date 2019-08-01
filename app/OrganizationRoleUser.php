<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
/**
 *   @OA\Schema(  
 *      required={"id", "organization_id", "user_id", "role_id"},
 *      @OA\Xml(name="OrganizationRoleUser"),
 *      
 *      @OA\Property( property="id", type="integer"),
 *      @OA\Property( property="organization_id", type="integer"),
 *      @OA\Property( property="user_id", type="integer"),
 *      @OA\Property( property="role_id", type="integer"),
 *      @OA\Property( property="created_at", type="string"),
 *      @OA\Property( property="updated_at", type="string"),
 *   ),
 * 
 */
class OrganizationRoleUser extends Model
{
    
    protected $guarded = [];
    
    
    public function organization()
    {
      return $this->belongsTo('Organization');
    }
    public function user()
    {
      return $this->belongsTo('User');
    }
    public function role()
    {
      return $this->belongsTo('Role');
    }
   
}

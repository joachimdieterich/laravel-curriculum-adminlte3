<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
/**
 *   @OA\Schema(  
 *      required={"id", "title", "begin", "end", "organization_type_id", "owner_id"},
 *      @OA\Xml(name="Period"),
 *      
 *      @OA\Property( property="id", type="integer"),
 *      @OA\Property( property="title", type="string"),
 *      @OA\Property( property="begin", type="string"),
 *      @OA\Property( property="end", type="string"),
 *      @OA\Property( property="organization_type_id", type="integer"),
 *      @OA\Property( property="owner_id", type="integer"),
 *      @OA\Property( property="created_at", type="string"),
 *      @OA\Property( property="updated_at", type="string")
 *   ),
 */
class Period extends Model
{
    public function organization()
    {
        return $this->hasOne('App\Organization', 'id', 'organization_id');
    }
    
    public function owner()
    {
        return $this->hasOne('App\User', 'id', 'owner_id');
    }
}

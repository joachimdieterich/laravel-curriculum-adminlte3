<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
/**
 *   @OA\Schema(  
 *      required={"id", "title", "external_begin", "external_end", "organization_type_id"},
 *      @OA\Xml(name="Grade"),
 *      
 *      @OA\Property( property="id", type="integer"),
 *      @OA\Property( property="title", type="string"),
 *      @OA\Property( property="external_begin", type="integer"),
 *      @OA\Property( property="external_end", type="integer"),
 *      @OA\Property( property="organization_type_id", type="integer"),
 *      @OA\Property( property="created_at", type="string"),
 *      @OA\Property( property="updated_at", type="string")
 *   ),
 */
class Grade extends Model
{
    //
}

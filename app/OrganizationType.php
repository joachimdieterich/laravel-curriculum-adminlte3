<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 *   @OA\Schema(
 *      required={"id", "title", "external_id", "state_id", "country_id"},
 *      @OA\Xml(name="OrganizationType"),
 *
 *      @OA\Property( property="id", type="integer"),
 *      @OA\Property( property="title", type="string"),
 *      @OA\Property( property="external_id", type="integer"),
 *      @OA\Property( property="state_id", type="string"),
 *      @OA\Property( property="country_id", type="string"),
 *   ),
 */
class OrganizationType extends Model
{
    protected $fillable = ['title', 'external_id', 'state_id', 'country_id', 'created_at', 'updated_at'];

    public function state()
    {
        return $this->hasOne('App\State', 'code', 'state_id');
    }

    public function country()
    {
        return $this->hasOne('App\Country', 'alpha2', 'country_id');
    }

    public function grades()
    {
        return $this->hasMany('App\Grade', 'organization_type_id', 'id');
    }
}

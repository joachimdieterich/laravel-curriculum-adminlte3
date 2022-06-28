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
    protected $guarded = [];

    public function path()
    {
        return route('periods.show', $this->id);
    }

    public function organization()
    {
        return $this->belongsToMany('App\Organization', 'groups')
            ->withPivot(['period_id', 'organization_id']);
        //return $this->hasMany('App\Period', 'organization_id', 'id');
    }

    public function owner()
    {
        return $this->hasOne('App\User', 'id', 'owner_id');
    }
}

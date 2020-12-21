<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 *   @OA\Schema(
 *      required={"id", "title", "title_short", "external_id", "organization_type_id", "organization_id"},
 *      @OA\Xml(name="Subject"),
 *
 *      @OA\Property( property="id", type="integer"),
 *      @OA\Property( property="title", type="string"),
 *      @OA\Property( property="title_short", type="string"),
 *      @OA\Property( property="external_id", type="integer"),
 *      @OA\Property( property="organization_type_id", type="integer"),
 *      @OA\Property( property="organization_id", type="integer"),
 *      @OA\Property( property="created_at", type="string"),
 *      @OA\Property( property="updated_at", type="string"),
 *   ),
 */
class Subject extends Model
{
    protected $guarded = [];

    public function path()
    {
        return route('subjects.show', $this->id);
    }
}

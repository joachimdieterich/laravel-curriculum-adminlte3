<?php
namespace App\Http\Controllers\Api\V1\OpenApiDefinitions;

/**
 *   @OA\Schema(
 *      required={"model", "url", "title"},
 *      @OA\Xml(name="Moodle"),
 *
 *      @OA\Property( property="model", type="string"),
 *      @OA\Property( property="url", type="string"),
 *      @OA\Property( property="title", type="string"),
 *   ),
 */
class Moodle
{
}

/**
 *   @OA\Schema(
 *      required={"id", "title"},
 *      @OA\Xml( name="SelectList"),
 *
 *      @OA\Property( property="id", type="integer"),
 *      @OA\Property( property="title", type="string"),
 *   ),
 */
class SelectList
{
}

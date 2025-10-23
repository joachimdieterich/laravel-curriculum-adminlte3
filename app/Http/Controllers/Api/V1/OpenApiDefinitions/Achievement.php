<?php
namespace App\Http\Controllers\Api\V1\OpenApiDefinitions;

/**
 * @OA\Get(
 *   path="/v1/achievements",
 *   tags={"Achievement v1"},
 *   summary="Get achievements from a single Enabling-Objective",
 *   security={
 *       {"passport": {"*"}},
 *   },
 *   @OA\Parameter(
 *     name="referenceable_id",
 *     description="ID's of the referenced enabling-objectives",
 *     required=true,
 *     in="query",
 *     @OA\Schema(type="array", @OA\Items(type="integer"))
 *   ),
 *   @OA\Parameter(
 *     name="scale",
 *     description="Title of the achievement scale, e.g. 'moodle'",
 *     required=true,
 *     in="query",
 *     @OA\Schema(type="string")
 *   ),
 *   @OA\Parameter(
 *     name="user_common_name",
 *     description="(optional) Only get achievements from given users",
 *     in="query",
 *     @OA\Schema(type="array", @OA\Items(type="string"))
 *   ),
 *   @OA\Response(
 *     response=200,
 *     description="successful operation",
 *     @OA\JsonContent(ref="#/components/schemas/Achievement")
 *   ),
 *   @OA\Response(response=400, description="Missing required fields | Invalid data format, expected array for referenceable_id and user_common_name"),
 *   @OA\Response(response=404, description="common_name or scale not found")
 * ),
 * @OA\Post(
 *   path="/v1/achievements",
 *   operationId="storeAchievement",
 *   tags={"Achievement v1"},
 *   summary="Create or update an achievement",
 *   security={
 *       {"passport": {"*"}},
 *   },
 *   @OA\Parameter(
 *     name="user_common_name",
 *     description="Common names of the users who achieved this",
 *     required=true,
 *     in="query",
 *     @OA\Schema(type="array", @OA\Items(type="string"))
 *   ),
 *   @OA\Parameter(
 *     name="owner_common_name",
 *     description="Common name of the user who set the status, e.g. a teacher",
 *     required=true,
 *     in="query",
 *     @OA\Schema(type="string")
 *   ),
 *   @OA\Parameter(
 *     name="referenceable_id",
 *     description="ID's of the referenced enabling-objectives",
 *     required=true,
 *     in="query",
 *     @OA\Schema(type="array", @OA\Items(type="integer"))
 *   ),
 *   @OA\Parameter(
 *     name="scale",
 *     description="Title of the achievement scale, e.g. 'moodle'",
 *     required=true,
 *     in="query",
 *     @OA\Schema(type="string")
 *   ),
 *   @OA\Parameter(
 *     name="status",
 *     description="Status value (max. 2 chars)",
 *     required=true,
 *     in="query",
 *     @OA\Schema(type="string")
 *   ),
 *   @OA\Response(
 *     response=200,
 *     description="successful operation",
 *     @OA\JsonContent(ref="#/components/schemas/Achievement")
 *   ),
 *   @OA\Response(response=400, description="Missing required fields | Invalid data format, expected array for referenceable_id and user_common_name"),
 *   @OA\Response(response=404, description="common_name or scale not found")
 * )
 */

class Achievement
{
}
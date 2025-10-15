<?php
namespace App\Http\Controllers\Api\V1\OpenApiDefinitions;

/**
 * @OA\Get(
 *   path="v1/achievements",
 *   tags={"Achievement v1"},
 *   summary="Get achievements from a single Enabling-Objective",
 *   security={
 *       {"passport": {"*"}},
 *   },
 *   @OA\Parameter(
 *     name="referenceable_id",
 *     description="ID of the referenced object, e.g. an EnablingObjective",
 *     required=true,
 *     in="query",
 *     @OA\Schema(type="integer", format="int64")
 *   ),
 *   @OA\Parameter(
 *     name="scale",
 *     description="Title of the achievement scale, e.g. 'moodle'",
 *     required=true,
 *     in="query",
 *     @OA\Schema(type="string")
 *   ),
 *   @OA\Response(
 *     response=200,
 *     description="successful operation",
 *     @OA\JsonContent(ref="#/components/schemas/Achievement")
 *   ),
 *   @OA\Response(response=400, description="Missing required fields"),
 *   @OA\Response(response=404, description="Scale not found")
 * ),
 * @OA\Post(
 *   path="v1/achievements",
 *   operationId="storeAchievement",
 *   tags={"Achievement v1"},
 *   summary="Create or update an achievement",
 *   security={
 *       {"passport": {"*"}},
 *   },
 *   @OA\Parameter(
 *     name="user_common_name",
 *     description="Common name of the user who achieved this",
 *     required=true,
 *     in="query",
 *     @OA\Schema(type="string")
 *   ),
 *   @OA\Parameter(
 *     name="owner_common_name",
 *     description="Common name of the user who set the status, e.g. a teacher",
 *     required=true,
 *     in="query",
 *     @OA\Schema(type="string")
 *   ),
 *   @OA\Parameter(
 *     name="referenceable_type",
 *     description="Type of the referenced object, e.g. 'App\EnablingObjective'",
 *     required=true,
 *     in="query",
 *     @OA\Schema(type="string")
 *   ),
 *   @OA\Parameter(
 *     name="referenceable_id",
 *     description="ID of the referenced object",
 *     required=true,
 *     in="query",
 *     @OA\Schema(type="integer", format="int64")
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
 *     description="Status value, e.g. '50' for 50%",
 *     required=true,
 *     in="query",
 *     @OA\Schema(type="string")
 *   ),
 *   @OA\Response(
 *     response=200,
 *     description="successful operation",
 *     @OA\JsonContent(ref="#/components/schemas/Achievement")
 *   ),
 *   @OA\Response(response=400, description="Missing required fields"),
 *   @OA\Response(response=404, description="common_name or scale not found")
 * )
 */

class Achievement
{
}
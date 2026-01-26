<?php
namespace App\Http\Controllers\Api\V1\OpenApiDefinitions;
/**
 * @OA\Get(
 *      path="/v1/moodle/getModelTypes",
 *      operationId="getModelTypes",
 *      tags={"Moodle v1"},
 *      summary="Get all model types",
 *      description="Returns a collection of available models",
 *      security={
 *           {"passport": {"*"}},
 *      },
 *      @OA\Response(
 *          response=200,
 *          description="successful operation",
 *          @OA\Schema(ref="#/components/schemas/Moodle"),
 *       ),
 *       @OA\Response(response=400, description="Bad request"),
 * )
 *
 *  @OA\Get(
 *      path="/v1/moodle/curricula",
 *      operationId="getCurricula",
 *      tags={"Moodle v1"},
 *      summary="Get curricula by common name",
 *      description="Returns a collection of global and owned curricula",
 *      security={
 *           {"passport": {"*"}},
 *      },
 *      @OA\Parameter(
 *          name="common_name",
 *          description="users common_name",
 *          required=true,
 *          in="query",
 *          @OA\Schema(
 *              type="string"
 *          )
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="successful operation",
 *          @OA\Schema(ref="#/components/schemas/SelectList"),
 *       ),
 *       @OA\Response(response=400, description="Bad request"),
 * )
 *
 * @OA\Get(
 *      path="/v1/moodle/curricula/{curriculum}/terminalObjectives",
 *      operationId="getTerminalObjectives",
 *      tags={"Moodle v1"},
 *      summary="Get terminal_objectives by curriculum_id and common_name",
 *      description="Returns a collection of available terminalObjectives of one curriculum",
 *      security={
 *           {"passport": {"*"}},
 *      },
 *     @OA\Parameter(
 *          name="curriculum",
 *          description="curriculum id",
 *          required=true,
 *          in="path",
 *          @OA\Schema(
 *              type="integer"
 *          )
 *      ),
 *      @OA\Parameter(
 *          name="common_name",
 *          description="users common_name",
 *          required=true,
 *          in="query",
 *          @OA\Schema(
 *              type="string"
 *          )
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="successful operation",
 *          @OA\Schema(ref="#/components/schemas/SelectList"),
 *       ),
 *       @OA\Response(response=400, description="Bad request"),
 * )
 *
 *
 * @OA\Get(
 *      path="/v1/moodle/curricula/terminalObjectives/{terminalObjective}/enablingObjectives",
 *      operationId="getEnablingObjectivesByTerminalObjectiveId",
 *      tags={"Moodle v1"},
 *      summary="Get enabling_objectives by terminal_objective_id and common_name",
 *      description="Returns a collection of available enablingObjectives of one curriculum",
 *      security={
 *           {"passport": {"*"}},
 *      },
 *     @OA\Parameter(
 *          name="terminalObjective",
 *          description="terminalObjectiveid",
 *          required=true,
 *          in="path",
 *          @OA\Schema(
 *              type="integer"
 *          )
 *      ),
 *      @OA\Parameter(
 *          name="common_name",
 *          description="users common_name",
 *          required=true,
 *          in="query",
 *          @OA\Schema(
 *              type="string"
 *          )
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="successful operation",
 *          @OA\Schema(ref="#/components/schemas/SelectList"),
 *       ),
 *       @OA\Response(response=400, description="Bad request"),
 * )
 *
 * @OA\Get(
 *      path="/v1/moodle/curricula/{curriculum}/enablingObjectives",
 *      operationId="getEnablingObjectives",
 *      tags={"Moodle v1"},
 *      summary="Get enabling_objectives by curriculum_id and common_name",
 *      description="Returns a collection of available enablingObjectives of one curriculum",
 *      security={
 *           {"passport": {"*"}},
 *      },
 *     @OA\Parameter(
 *          name="curriculum",
 *          description="curriculum id",
 *          required=true,
 *          in="path",
 *          @OA\Schema(
 *              type="integer"
 *          )
 *      ),
 *      @OA\Parameter(
 *          name="common_name",
 *          description="users common_name",
 *          required=true,
 *          in="query",
 *          @OA\Schema(
 *              type="string"
 *          )
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="successful operation",
 *          @OA\Schema(ref="#/components/schemas/SelectList"),
 *       ),
 *       @OA\Response(response=400, description="Bad request"),
 * )
 *
 * @OA\Get(
 *      path="/v1/moodle/logbooks",
 *      operationId="getLogbooks",
 *      tags={"Moodle v1"},
 *      summary="Get logbooks by common_name",
 *      description="Returns a collection of available logbooks of user",
 *      security={
 *           {"passport": {"*"}},
 *      },
 *      @OA\Parameter(
 *          name="common_name",
 *          description="users common_name",
 *          required=true,
 *          in="query",
 *          @OA\Schema(
 *              type="string"
 *          )
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="successful operation",
 *          @OA\Schema(ref="#/components/schemas/SelectList"),
 *       ),
 *       @OA\Response(response=400, description="Bad request"),
 * )
 *
 * @OA\Get(
 *      path="/v1/moodle/kanbans",
 *      operationId="getKanbans",
 *      tags={"Moodle v1"},
 *      summary="Get kanbans by common_name",
 *      description="Returns a collection of available kanbans of user",
 *      security={
 *           {"passport": {"*"}},
 *      },
 *      @OA\Parameter(
 *          name="common_name",
 *          description="users common_name",
 *          required=true,
 *          in="query",
 *          @OA\Schema(
 *              type="string"
 *          )
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="successful operation",
 *          @OA\Schema(ref="#/components/schemas/SelectList"),
 *      ),
 *      @OA\Response(response=400, description="Bad request"),
 * )
 * 
 * @OA\Post(
 *      path="/v1/moodle/groups/enrol",
 *      operationId="enrolGroup",
 *      tags={"Moodle v1"},
 *      summary="Enrol groups to different resources",
 *      description="Creates or updates group-subscriptions to Kanbans/Logbooks and returns the subscriptions",
 *      security={
 *           {"passport": {"*"}},
 *      },
 *      @OA\RequestBody(
 *          required=true,
 *          @OA\JsonContent(
 *              required={"common_name", "groups"},
 *              @OA\Property(
 *                  property="common_name",
 *                  description="user common_name",
 *                  type="string"
 *              ),
 *              @OA\Property(
 *                  property="groups",
 *                  description="array of group common_names",
 *                  type="array",
 *                  @OA\Items(type="string")
 *              ),
 *              @OA\Property(
 *                  property="kanbans",
 *                  description="array of kanban IDs",
 *                  type="array",
 *                  @OA\Items(type="integer")
 *              ),
 *              @OA\Property(
 *                  property="logbooks",
 *                  description="array of logbook IDs",
 *                  type="array",
 *                  @OA\Items(type="integer")
 *              ),
 *              @OA\Property(
 *                  property="editable",
 *                  description="allow users to create/edit content inside the ressource",
 *                  default=false,
 *                  type="boolean"
 *              ),
 *          )
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="successful operation",
 *          @OA\Schema(ref="#/components/schemas/SelectList"),
 *      ),
 *      @OA\Response(response=400, description="Bad request"),
 * )
 * 
 * @OA\Post(
 *      path="/v1/moodle/users/enrol",
 *      operationId="enrolUsers",
 *      tags={"Moodle v1"},
 *      summary="Enrol users to different resources",
 *      description="Creates or updates user-subscriptions to Kanbans/Curricula/Groups and returns the amount of created/updated subscriptions",
 *      security={
 *           {"passport": {"*"}},
 *      },
 *      @OA\RequestBody(
 *          required=true,
 *          description="at least one model (Kanbans/Curricula/Groups) needs to be provided",
 *          @OA\JsonContent(
 *              required={"users"},
 *              @OA\Property(
 *                  property="users",
 *                  description="array/array-like string (e.g. ''['common_name']'') of user common_names",
 *                  type="array",
 *                  @OA\Items(type="string")
 *              ),
 *              @OA\Property(
 *                  property="groups",
 *                  description="array/array-like string of group common_names | will create new groups if not existing",
 *                  type="array",
 *                  @OA\Items(type="string")
 *              ),
 *              @OA\Property(
 *                  property="kanbans",
 *                  description="array/array-like string of kanban IDs",
 *                  type="array",
 *                  @OA\Items(type="integer")
 *              ),
 *              @OA\Property(
 *                  property="curricula",
 *                  description="array/array-like string of curriculum IDs",
 *                  type="array",
 *                  @OA\Items(type="integer")
 *              ),
 *              @OA\Property(
 *                  property="editable",
 *                  description="allow users to create/edit content inside the ressource",
 *                  default=false,
 *                  type="boolean"
 *              ),
 *          )
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="successful operation",
 *          @OA\Schema(type="integer"),
 *      ),
 *      @OA\Response(response=400, description="Bad request"),
 * )
 * 
 * @OA\Post(
 *      path="/v1/moodle/users/expel",
 *      operationId="expelUsers",
 *      tags={"Moodle v1"},
 *      summary="Expel users from different resources",
 *      description="Deletes user-subscriptions from Kanbans/Curricula/Groups and returns the amount of deleted subscriptions",
 *      security={
 *           {"passport": {"*"}},
 *      },
 *      @OA\RequestBody(
 *          required=true,
 *          description="at least one model (Kanbans/Curricula/Groups) needs to be provided",
 *          @OA\JsonContent(
 *              required={"users"},
 *              @OA\Property(
 *                  property="users",
 *                  description="array/array-like string (e.g. ''['common_name']'') of user common_names",
 *                  type="array",
 *                  @OA\Items(type="string")
 *              ),
 *              @OA\Property(
 *                  property="groups",
 *                  description="array/array-like string of group common_names | missing groups will be ignored",
 *                  type="array",
 *                  @OA\Items(type="string")
 *              ),
 *              @OA\Property(
 *                  property="kanbans",
 *                  description="array/array-like string of kanban IDs",
 *                  type="array",
 *                  @OA\Items(type="integer")
 *              ),
 *              @OA\Property(
 *                  property="curricula",
 *                  description="array/array-like string of curriculum IDs",
 *                  type="array",
 *                  @OA\Items(type="integer")
 *              ),
 *          )
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="successful operation",
 *          @OA\Schema(type="integer"),
 *      ),
 *      @OA\Response(response=400, description="Bad request"),
 * )
 */
class MoodleDefinitions
{
}
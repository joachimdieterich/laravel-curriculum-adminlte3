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
 *      summary="Get currcula by common name",
 *      description="Returns a collection of available curricula",
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
 *       ),
 *       @OA\Response(response=400, description="Bad request"),
 * )
 */
class MoodleDefinitions
{
}



<?php
namespace App\Http\Controllers\Api\V1\OpenApiDefinitions;

/**
* @OA\Get(
*      path="/v1/kanbans?owner_cn={owner_cn}",
*      operationId="getUserKanbans",
*      tags={"Kanban v1"},
*      summary="Get user kanbans",
*      description="Returns a collection of users kanban objects",
*      security={
*           {"passport": {"*"}},
*      },
*      @OA\Parameter(
*          name="owner_cn",
*          description="Owner Cn",
*          required=true,
*          in="path",
*          @OA\Schema(
*              type="string"
*          )
*      ),
*      @OA\Response(
*          response=200,
*          description="successful operation",
*          @OA\Schema(ref="#/components/schemas/Kanbans"),
*       ),
*       @OA\Response(response=400, description="Bad request"),
* )
*
* @OA\Post(
*      path="/v1/kanbans",
*      operationId="createKanban",
*      tags={"Kanban v1"},
*      summary="Create new kanban",
*      description="Returns the new kanban object",
*      security={
*           {"passport": {"*"}},
*      },
*      @OA\Parameter(
*          name="owner_cn",
*          description="common_name of Kanban-owner",
*          required=true,
*          in="query"
*      ),
*      @OA\Parameter(
*          name="title",
*          description="Kanban title",
*          required=true,
*          in="query"
*      ),
*      @OA\Parameter(
*          name="description",
*          description="Kanban description",
*          in="query"
*      ),
*      @OA\Parameter(
*          name="color",
*          description="Color",
*          example="#2980B9",
*          in="query"
*      ),
*      @OA\Parameter(
*          name="editable",
*          description="Return the token-link with edit-rights",
*          example="0 or 1",
*          in="query"
*      ),
*      @OA\Response(
*          response=200,
*          description="successful operation",
*          @OA\JsonContent(ref="#/components/schemas/Kanban"),
*       ),
*       @OA\Response(response=400, description="Missing/Empty attribute [owner_cn] or [title]"),
*       @OA\Response(response=404, description="owner_cn not found"),
* )
*
* @OA\Put(
*      path="/v1/kanbans/{kanban}",
*      operationId="updateKanbanById",
*      tags={"Kanban v1"},
*      summary="Edit kanban",
*      description="Edit kanban",
*      security={
*           {"passport": {"*"}},
*      },
*      @OA\Parameter(
*          name="kanban",
*          description="Kanban id",
*          required=true,
*          in="path",
*          @OA\Schema(
*              type="integer"
*          )
*      ),
*      @OA\RequestBody(
*       required=true,
*       @OA\MediaType(
*           mediaType="application/x-www-form-urlencoded",
*           @OA\Schema(
*               type="object",
*               @OA\Property(
*                   property="title",
*                   description="Kanban title",
*                   type="string"
*               ),
*               @OA\Property(
*                   property="description",
*                   description="Kanban description",
*                   type="string"
*               ),
*               @OA\Property(
*                   property="color",
*                   description="color",
*                   type="string"
*               ),
*               @OA\Property(
*                   property="owner_cn",
*                   description="Owner Cn",
*                   type="string"
*               )
*           )
*       )
*   ),
*
*       @OA\Response(
*          response=200,
*          description="successful operation",
*          @OA\JsonContent(ref="#/components/schemas/Kanban")
*       ),
*       @OA\Response(response=400, description="Bad request"),
*
* )
*
* @OA\Get(
*      path="/v1/kanbans/{kanban}",
*      operationId="getKanban",
*      tags={"Kanban v1"},
*      summary="Get kanban by Id",
*      description="Returns a kanban object",
*      security={
*           {"passport": {"*"}},
*      },
*      @OA\Parameter(
*          name="kanban",
*          description="Kanban id",
*          required=true,
*          in="path",
*          @OA\Schema(
*              type="integer"
*          )
*      ),
*      @OA\Response(
*          response=200,
*          description="successful operation",
*          @OA\JsonContent(ref="#/components/schemas/Kanban"),
*       ),
*       @OA\Response(response=400, description="Bad request"),
* )
*
* @OA\Delete(
*      path="/v1/kanbans/{kanban}",
*      operationId="deleteKanban",
*      tags={"Kanban v1"},
*      summary="Delete kanban by ID",
*      description="Delete a kanban object",
*      security={
*           {"passport": {"*"}},
*      },
*      @OA\Parameter(
*          name="kanban",
*          description="Kanban ID",
*          required=true,
*          in="path",
*          @OA\Schema(
*              type="integer"
*          )
*      ),
*      @OA\Parameter(
*          name="owner_cn",
*          description="common_name of Kanban-owner",
*          required=true,
*          in="query",
*          @OA\Schema(
*              type="string"
*          )
*      ),
*      @OA\Response(response=200, description="successful operation"),
*      @OA\Response(response=400, description="Missing attribute [owner_cn]"),
*      @OA\Response(response=403, description="common_name does not match Kanban-owner's common_name")
* )
*
* @OA\Post(
*        path="/v1/kanbans/{kanban}/enrol",
*        operationId="enrolToKanban",
*        tags={"Kanban v1"},
*        summary="Enrol user/group/organization to existing kanban",
*        description="Enrol user/group/organization to existing kanban",
*        security={
*             {"passport": {"*"}},
*        },
*      @OA\Parameter(
*            name="kanban",
*            description="Kanban id",
*            required=true,
*            in="path",
*            @OA\Schema(
*                type="integer"
*            )
*        ),
*        @OA\RequestBody(
*         required=true,
*         @OA\MediaType(
*             mediaType="application/x-www-form-urlencoded",
*             @OA\Schema(
*                 type="object",
*                 required={"subscribable_type, common_name, editable"},
*                 @OA\Property(
*                     property="subscribable_type",
*                     description="subscribable_type",
*                     type="string"
*                 ),
*                 @OA\Property(
*                     property="common_name",
*                     description="common_name",
*                     type="string"
*                 ),
*                 @OA\Property(
*                     property="editable",
*                     description="editable",
*                     type="boolean"
*                 ),
*             )
*         )
*     ),
*            @OA\Response(
*            response=200,
*            description="successful operation",
*            @OA\JsonContent(ref="#/components/schemas/KanbanSubscription"),
*         ),
*         @OA\Response(response=400, description="Bad request"),
*  )
*
* @OA\Post(
*        path="/v1/kanbans/{kanban}/expel",
*        operationId="expelFromKanban",
*        tags={"Kanban v1"},
*        summary="Expel user/group/organization from kanban",
*        description="Expel user/group/organization from kanban",
*        security={
*             {"passport": {"*"}},
*        },
*      @OA\Parameter(
*            name="kanban",
*            description="Kanban id",
*            required=true,
*            in="path",
*            @OA\Schema(
*                type="integer"
*            )
*        ),
*        @OA\RequestBody(
*         required=true,
*         @OA\MediaType(
*             mediaType="application/x-www-form-urlencoded",
*             @OA\Schema(
*                 type="object",
*                 required={"subscribable_type, common_name"},
*                 @OA\Property(
*                     property="subscribable_type",
*                     description="subscribable_type",
*                     type="string"
*                 ),
*                 @OA\Property(
*                      property="common_name",
*                      description="common_name",
*                      type="string"
*                  ),
*             )
*         )
*     ),
*            @OA\Response(
*            response=200,
*            description="successful operation",
*            @OA\JsonContent(ref="#/components/schemas/KanbanSubscription"),
*         ),
*         @OA\Response(response=400, description="Bad request"),
*  )
*
*
* @OA\Get(
*        path="/v1/kanbans/{kanban}/subscriptions",
*        operationId="getKanbanSubscriptions",
*        tags={"Kanban v1"},
*        summary="Get kanban subscriptions by Id",
*        description="Returns a kanban subscriptions",
*        security={
*             {"passport": {"*"}},
*        },
*        @OA\Parameter(
*            name="kanban",
*            description="Kanban id",
*            required=true,
*            in="path",
*            @OA\Schema(
*                type="integer"
*            )
*        ),
*        @OA\Response(
*            response=200,
*            description="successful operation",
*            @OA\JsonContent(ref="#/components/schemas/KanbanSubscription"),
*         ),
*         @OA\Response(response=400, description="Bad request"),
*   )
*/

class Kanban
{
}

<?php
namespace App\Http\Controllers\Api\V1\OpenApiDefinitions;

/**
* @OA\Get(
*      path="/v1/roles",
*      operationId="getAllRoles",
*      tags={"Role v1"},
*      summary="Get all roles",
*      description="Returns a collection of role objects",
*      security={
*           {"passport": {"*"}},
*      },
*      @OA\Response(
*          response=200,
*          description="successful operation",
*          @OA\Schema(ref="#/components/schemas/Roles"),
*       ),
*       @OA\Response(response=400, description="Bad request"),
* )
*
* @OA\Get(
*      path="/v1/roles/{role}",
*      operationId="getRole",
*      tags={"Role v1"},
*      summary="Get a role by Id",
*      description="Returns a role by Id",
*      security={
*           {"passport": {"*"}},
*      },
*      @OA\Parameter(
*          name="role",
*          description="Role id",
*          required=true,
*          in="path",
*          @OA\Schema(
*              type="integer"
*          )
*      ),
*      @OA\Response(
*          response=200,
*          description="successful operation",
*          @OA\JsonContent(ref="#/components/schemas/Role"),
*       ),
*       @OA\Response(
*          response=400,
*          description="Bad request",
*       ),
*       @OA\Response(
*          response=404,
*          description="Bad request",
*         @OA\JsonContent(),
*       ),
* )
*/

class Role
{
}

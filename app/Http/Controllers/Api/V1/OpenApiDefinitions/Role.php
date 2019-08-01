<?php

/**
* @OA\Get(
*      path="/v1/roles",
*      operationId="roles",
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
*/

/**
* @OA\Get(
*      path="/v1/roles/{id}",
*      operationId="roles",
*      tags={"Role v1"},
*      summary="Get all roles",
*      description="Returns a role objects",
*      security={
*           {"passport": {"*"}},
*      },
*      @OA\Parameter(
*          name="id",
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
*
*/

<?php
namespace App\Http\Controllers\Api\V1\OpenApiDefinitions;

/**
* @OA\Get(
*      path="/v1/states",
*      operationId="getAllStates",
*      tags={"State v1"},
*      summary="Get all states",
*      description="Returns a collection of state objects",
*      security={
*           {"passport": {"*"}},
*      },
*      @OA\Response(
*          response=200,
*          description="successful operation",
*          @OA\Schema(ref="#/components/schemas/State"),
*       ),
*       @OA\Response(response=400, description="Bad request"),
* )
*
* @OA\Get(
*      path="/v1/states/{state}",
*      operationId="getState",
*      tags={"State v1"},
*      summary="Get a state by Id",
*      description="Returns a state by Id",
*      security={
*           {"passport": {"*"}},
*      },
*      @OA\Parameter(
*          name="state",
*          description="State Id",
*          required=true,
*          in="path",
*          @OA\Schema(
*              type="integer"
*          )
*      ),
*      @OA\Response(
*          response=200,
*          description="successful operation",
*          @OA\JsonContent(ref="#/components/schemas/State"),
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

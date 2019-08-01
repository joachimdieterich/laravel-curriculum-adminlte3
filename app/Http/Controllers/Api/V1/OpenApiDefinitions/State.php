<?php

/**
* @OA\Get(
*      path="/v1/states",
*      operationId="states",
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
*/

/**
* @OA\Get(
*      path="/v1/states/{id}",
*      operationId="states",
*      tags={"State v1"},
*      summary="Get all states",
*      description="Returns a state objects",
*      security={
*           {"passport": {"*"}},
*      },
*      @OA\Parameter(
*          name="id",
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
*
*/

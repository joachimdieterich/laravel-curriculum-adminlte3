<?php

/**
* @OA\Get(
*      path="/v1/countries",
*      operationId="countries",
*      tags={"Country v1"},
*      summary="Get all countries",
*      description="Returns a collection of country objects",
*      security={
*           {"passport": {"*"}},
*      },
*      @OA\Response(
*          response=200,
*          description="successful operation",
*          @OA\Schema(ref="#/components/schemas/Country"),
*       ),
*       @OA\Response(response=400, description="Bad request"),
* )
*
*/

/**
* @OA\Get(
*      path="/v1/countries/{id}",
*      operationId="countries",
*      tags={"Country v1"},
*      summary="Get all countries",
*      description="Returns a countrie objects",
*      security={
*           {"passport": {"*"}},
*      },
*      @OA\Parameter(
*          name="id",
*          description="Country Id",
*          required=true,
*          in="path",
*          @OA\Schema(
*              type="integer"
*          )
*      ),
*      @OA\Response(
*          response=200,
*          description="successful operation",
*          @OA\JsonContent(ref="#/components/schemas/Country"),
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

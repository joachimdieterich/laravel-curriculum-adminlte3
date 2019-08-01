<?php

/**
* @OA\Get(
*      path="/v1/subjects",
*      operationId="subject",
*      tags={"Subject v1"},
*      summary="Get all subject",
*      description="Returns a collection of subject objects",
*      security={
*           {"passport": {"*"}},
*      },
*      @OA\Response(
*          response=200,
*          description="successful operation",
*          @OA\Schema(ref="#/components/schemas/Subject"),
*       ),
*       @OA\Response(response=400, description="Bad request"),
* )
*
*/

/**
* @OA\Get(
*      path="/v1/subjects/{id}",
*      operationId="subject",
*      tags={"Subject v1"},
*      summary="Get all subject",
*      description="Returns a subject objects",
*      security={
*           {"passport": {"*"}},
*      },
*      @OA\Parameter(
*          name="id",
*          description="Subject Id",
*          required=true,
*          in="path",
*          @OA\Schema(
*              type="integer"
*          )
*      ),
*      @OA\Response(
*          response=200,
*          description="successful operation",
*          @OA\JsonContent(ref="#/components/schemas/Subject"),
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

<?php
namespace App\Http\Controllers\Api\V1\OpenApiDefinitions;

/**
* @OA\Get(
*      path="/v1/subjects",
*      operationId="getAllSubject",
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
* @OA\Get(
*      path="/v1/subjects/{subject}",
*      operationId="getSubject",
*      tags={"Subject v1"},
*      summary="Get  a subject by Id",
*      description="Returns a subject by Id",
*      security={
*           {"passport": {"*"}},
*      },
*      @OA\Parameter(
*          name="subject",
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
*/

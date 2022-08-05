<?php
namespace App\Http\Controllers\Api\V1\OpenApiDefinitions;

/**
* @OA\Get(
*      path="/v1/curricula",
*      operationId="getAllCurricula",
*      tags={"Curriculum v1"},
*      summary="Get all curricula",
*      description="Returns a collection of curriculum objects",
*      security={
*           {"passport": {"*"}},
*      },
*      @OA\Response(
*          response=200,
*          description="successful operation",
*          @OA\Schema(ref="#/components/schemas/Curriculum"),
*       ),
*       @OA\Response(response=400, description="Bad request"),
* )
*
* @OA\Get(
*      path="/v1/curricula/{curriculum}",
*      operationId="getCurriculum",
*      tags={"Curriculum v1"},
*      summary="Get curriculum by Id",
*      description="Returns a curriculum object",
*      security={
*           {"passport": {"*"}},
*      },
*      @OA\Parameter(
*          name="curriculum",
*          description="Curriculum Id",
*          required=true,
*          in="path",
*          @OA\Schema(
*              type="integer"
*          )
*      ),
*      @OA\Response(
*          response=200,
*          description="successful operation",
*          @OA\JsonContent(ref="#/components/schemas/Curriculum"),
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
* @OA\Get(
*      path="/v1/curricula/{curriculum}/metadataset?password={password}",
*      operationId="getSingleMetadataset",
*      tags={"Curriculum v1"},
*      summary="Get metadataset of curriculum by Id",
*      description="Returns a metadataset of a curriculum object",
*      security={
*           {"passport": {"*"}},
*      },
*      @OA\Parameter(
*          name="curriculum",
*          description="Curriculum Id",
*          required=true,
*          in="path",
*          @OA\Schema(
*              type="integer"
*          )
*      ),
*     @OA\Parameter(
*          name="password",
*          description="metadata password",
*          required=true,
*          in="path",
*          @OA\Schema(
*              type="string"
*          )
*      ),
*      @OA\Response(
*          response=200,
*          description="successful operation",
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
* @OA\Get(
*      path="/v1/curricula/metadatasets?password={password}",
*      operationId="getAllMetadatasets",
*      tags={"Curriculum v1"},
*      summary="Get all metadataset of existing curricula",
*      description="Returns a array of metadatasets of all curriculum objects",
*      security={
*           {"passport": {"*"}},
*      },
*     @OA\Parameter(
*          name="password",
*          description="metadata password",
*          required=true,
*          in="path",
*          @OA\Schema(
*              type="string"
*          )
*      ),
*      @OA\Response(
*          response=200,
*          description="successful operation",
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

class Curriculum
{
}

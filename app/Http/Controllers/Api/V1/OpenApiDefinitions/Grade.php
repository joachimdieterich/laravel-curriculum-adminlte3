<?php
namespace App\Http\Controllers\Api\V1\OpenApiDefinitions;

/**
* @OA\Get(
*      path="/v1/grades",
*      operationId="getAllGrades",
*      tags={"Grade v1"},
*      summary="Get all grades",
*      description="Returns a collection of grade objects",
*      security={
*           {"passport": {"*"}},
*      },
*      @OA\Response(
*          response=200,
*          description="successful operation",
*          @OA\Schema(ref="#/components/schemas/Grade"),
*       ),
*       @OA\Response(response=400, description="Bad request"),
* )
*
* @OA\Get(
*      path="/v1/grades/{grade}",
*      operationId="getGrade",
*      tags={"Grade v1"},
*      summary="Get grade by Id",
*      description="Returns a grade objects",
*      security={
*           {"passport": {"*"}},
*      },
*      @OA\Parameter(
*          name="grade",
*          description="Grade Id",
*          required=true,
*          in="path",
*          @OA\Schema(
*              type="integer"
*          )
*      ),
*      @OA\Response(
*          response=200,
*          description="successful operation",
*          @OA\JsonContent(ref="#/components/schemas/Grade"),
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

class Grade
{
}

<?php
namespace App\Http\Controllers\Api\V1\OpenApiDefinitions;

/**
* @OA\Get(
*      path="/v1/periods",
*      operationId="getAllPeriods",
*      tags={"Period v1"},
*      summary="Get all periods",
*      description="Returns a collection of period objects",
*      security={
*           {"passport": {"*"}},
*      },
*      @OA\Response(
*          response=200,
*          description="successful operation",
*          @OA\Schema(ref="#/components/schemas/Period"),
*       ),
*       @OA\Response(response=400, description="Bad request"),
* )
*
* @OA\Get(
*      path="/v1/periods/{period}",
*      operationId="getPeriod",
*      tags={"Period v1"},
*      summary="Get a period by Id",
*      description="Returns a period by Id",
*      security={
*           {"passport": {"*"}},
*      },
*      @OA\Parameter(
*          name="period",
*          description="Period Id",
*          required=true,
*          in="path",
*          @OA\Schema(
*              type="integer"
*          )
*      ),
*      @OA\Response(
*          response=200,
*          description="successful operation",
*          @OA\JsonContent(ref="#/components/schemas/Period"),
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
* @OA\Post(
*      path="/v1/periods",
*      operationId="createPeriod",
*      tags={"Period v1"},
*      summary="Create new period",
*      description="Returns a the new period object",
*      security={
*           {"passport": {"*"}},
*      },
*      @OA\RequestBody(
*       required=true,
*       @OA\MediaType(
*           mediaType="application/x-www-form-urlencoded",
*           @OA\Schema(
*               type="object",
*               required={"title", "begin", "end"},
*               @OA\Property(
*                   property="title",
*                   description="Group title",
*                   type="string"
*               ),
*               @OA\Property(
*                   property="begin",
*                   description="Begin of period",
*                   type="timestamp"
*               ),
*               @OA\Property(
*                   property="end",
*                   description="End of period",
*                   type="timestamp",
*               ),
*               @OA\Property(
*                   property="organization_id",
*                   description="Organization Id",
*                   type="integer"
*               ),
*               @OA\Property(
*                   property="owner_id",
*                   description="Owner Id",
*                   type="integer"
*               ),
*           )
*       )
*   ),
*
*      @OA\Response(
*          response=200,
*          description="successful operation",
*          @OA\JsonContent(ref="#/components/schemas/Period"),
*       ),
*       @OA\Response(response=400, description="Bad request"),
*
* )
*
* @OA\Put(
*      path="/v1/periods/{period}",
*      operationId="updatePeriodById",
*      tags={"Period v1"},
*      summary="Edit period",
*      description="Edit period",
*      security={
*           {"passport": {"*"}},
*      },
*      @OA\Parameter(
*          name="period",
*          description="Period id",
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
*                   description="Period title",
*                   type="string"
*               ),
*               @OA\Property(
*                   property="begin",
*                   description="Begin of period",
*                   type="timestamp"
*               ),
*               @OA\Property(
*                   property="end",
*                   description="End of period",
*                   type="timestamp",
*               ),
*               @OA\Property(
*                   property="organization_id",
*                   description="Organization Id",
*                   type="integer"
*               ),
*               @OA\Property(
*                   property="owner_id",
*                   description="Owner Id",
*                   type="integer"
*               ),
*           )
*       )
*   ),
*
*       @OA\Response(
*          response=200,
*          description="successful operation",
*          @OA\JsonContent(ref="#/components/schemas/Period")
*       ),
*       @OA\Response(response=400, description="Bad request"),
*
* )
*
* @OA\Delete(
*      path="/v1/periods/{period}",
*      operationId="deletePeriod",
*      tags={"Period v1"},
*      summary="Delete period by Id",
*      description="Delete a period object",
*      security={
*           {"passport": {"*"}},
*      },
*      @OA\Parameter(
*          name="period",
*          description="Period Id",
*          required=true,
*          in="path",
*          @OA\Schema(
*              type="integer"
*          )
*      ),
*
*      @OA\Response(
*          response=200,
*          description="successful operation",
*       ),
*       @OA\Response(response=400, description="Bad request"),
*
* )
*/

class Period
{
}

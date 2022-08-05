<?php
namespace App\Http\Controllers\Api\V1\OpenApiDefinitions;

/**
* @OA\Get(
*      path="/v1/organizationtypes",
*      operationId="getAllOrganizationtypes",
*      tags={"OrganizationType v1"},
*      summary="Get all organizationtypes",
*      description="Returns a collection of organizationtype objects",
*      security={
*           {"passport": {"*"}},
*      },
*      @OA\Response(
*          response=200,
*          description="successful operation",
*          @OA\Schema(ref="#/components/schemas/OrganizationType"),
*       ),
*       @OA\Response(response=400, description="Bad request"),
* )
*
* @OA\Get(
*      path="/v1/organizationtypes/{organizationtype}",
*      operationId="getOrganizationtype",
*      tags={"OrganizationType v1"},
*      summary="Get a organizationtype by Id",
*      description="Returns a organizationtype by Id",
*      security={
*           {"passport": {"*"}},
*      },
*      @OA\Parameter(
*          name="organizationtype",
*          description="OrganizationType Id",
*          required=true,
*          in="path",
*          @OA\Schema(
*              type="integer"
*          )
*      ),
*      @OA\Response(
*          response=200,
*          description="successful operation",
*          @OA\JsonContent(ref="#/components/schemas/OrganizationType"),
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

class Organizationtype
{
}

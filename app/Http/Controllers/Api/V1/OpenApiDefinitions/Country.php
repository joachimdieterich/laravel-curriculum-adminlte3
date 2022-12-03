<?php
namespace App\Http\Controllers\Api\V1\OpenApiDefinitions;

/**
* @OA\Get(
*      path="/v1/countries",
*      operationId="getAllCountries",
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
* @OA\Get(
*      path="/v1/countries/{country}",
*      operationId="getCountry",
*      tags={"Country v1"},
*      summary="Get country by Id",
*      description="Returns a country object",
*      security={
*           {"passport": {"*"}},
*      },
*      @OA\Parameter(
*          name="country",
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
*/

class Country
{
}

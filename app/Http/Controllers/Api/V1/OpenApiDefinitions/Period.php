<?php

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
*/

/**
* @OA\Get(
*      path="/v1/periods/{period}",
*      operationId="getPeriod",
*      tags={"Period v1"},
*      summary="Get all periods",
*      description="Returns a period objects",
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
*/

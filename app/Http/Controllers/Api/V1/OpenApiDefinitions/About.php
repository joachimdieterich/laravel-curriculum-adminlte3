<?php

/**
* @OA\Get(
*      path="/v1/about",
*      operationId="about",
*      tags={"About v1"},
*      summary="Get about",
*      description="Returns About",
*      security={
*           {"passport": {"*"}},
*      },
*      @OA\Response(
*          response=200,
*          description="Successful operation",
*       ),
*       @OA\Response(response=400, description="Bad request"),
*     )
*/
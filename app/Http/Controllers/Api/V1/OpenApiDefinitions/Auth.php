<?php

/**
* @OA\Get(
*      path="/v1/user",
*      operationId="user",
*      tags={"Auth v1"},
*      summary="Get the authenticated User",
*      description="Returns a user object",
*     
*      @OA\Response(
*          response=200,
*          description="successful operation"
*       ),
*       @OA\Response(response=400, description="Bad request"),
*     )
*
* Returns user object
*/
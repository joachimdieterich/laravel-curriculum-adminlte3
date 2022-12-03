<?php
namespace App\Http\Controllers\Api\V1\OpenApiDefinitions;

/**
* @OA\Post(
*      path="/v1/login",
*      operationId="login",
*      tags={"Auth v1"},
*      summary="Login a user",
*      description="Login a user",
*      @OA\Parameter(
*          name="email",
*          description="User email",
*          required=true,
*          in="path",
*          @OA\Schema(
*              type="string"
*          )
*      ),
*      @OA\Parameter(
*          name="password",
*          description="User password",
*          required=true,
*          in="path",
*          @OA\Schema(
*              type="string"
*          )
*      ),
*      @OA\Parameter(
*          name="remember_me",
*          description="User password",
*          required=false,
*          in="path",
*          @OA\Schema(
*              type="boolean"
*          )
*      ),
*      @OA\Response(
*          response=200,
*          description="successful operation"
*       ),
*       @OA\Response(response=400, description="Bad request"),
*     )
*
* Returns strings: access_token, token_type, expires at
*
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

class Auth
{
}

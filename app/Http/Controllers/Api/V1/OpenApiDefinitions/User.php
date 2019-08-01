<?php

/**
* @OA\Get(
*      path="/v1/users",
*      operationId="users",
*      tags={"User v1"},
*      summary="Get all users",
*      description="Returns a collection of user objects",
*      security={
*           {"passport": {"*"}},
*      },
*      @OA\Response(
*          response=200,
*          description="successful operation",
*          @OA\Schema(ref="#/components/schemas/Users"),
*       ),
*       @OA\Response(response=400, description="Bad request"),
* )
*
*/

/**
* @OA\POST(
*      path="/v1/users",
*      operationId="users",
*      tags={"User v1"},
*      summary="Create new user",
*      description="Returns a the new user object",
*      security={
*           {"passport": {"*"}},
*      },
*      @OA\RequestBody(
*       required=true,
*       @OA\MediaType(
*           mediaType="application/x-www-form-urlencoded",
*           @OA\Schema(
*               type="object",
*               required={"username", "firstname", "lastname", "email", "password"},
*               @OA\Property(
*                   property="common_name",
*                   description="common_name",
*                   type="string"
*               ),
*               @OA\Property(
*                   property="username",
*                   description="username",
*                   type="string"
*               ),
*               @OA\Property(
*                   property="firstname",
*                   description="firstname",
*                   type="string"
*               ),
*               @OA\Property(
*                   property="lastname",
*                   description="lastname",
*                   type="string"
*               ),
*               @OA\Property(
*                   property="email",
*                   description="email",
*                   type="string"
*               ),
*               @OA\Property(
*                   property="email_verified_at",
*                   description="email_verified_at",
*                   type="string"
*               ),
*               @OA\Property(
*                   property="password",
*                   description="password",
*                   type="string"
*               ),
*               @OA\Property(
*                   property="status_id",
*                   description="status_id",
*                   type="string"
*               ),
*               @OA\Property(
*                   property="current_organization_id",
*                   description="current_organization_id",
*                   type="string"
*               ),
*               @OA\Property(
*                   property="current_period_id",
*                   description="current_period_id",
*                   type="string"
*               ) 
*           )
*       )
*   ),
*      
*      @OA\Response(
*          response=200,
*          description="successful operation",
*          @OA\JsonContent(ref="#/components/schemas/User"),
*       ),
*       @OA\Response(response=400, description="Bad request"),
*       security={
*           {"api_key_security_example": {}}
*       }
*      
* )
*/


/**
* @OA\PUT(
*      path="/v1/users/{id}",
*      operationId="users",
*      tags={"User v1"},
*      summary="Edit user",
*      description="Edit user",
*      security={
*           {"passport": {"*"}},
*      },
*      @OA\Parameter(
*          name="id",
*          description="User id",
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
*                   property="common_name",
*                   description="common_name",
*                   type="string"
*               ),
*               @OA\Property(
*                   property="username",
*                   description="username",
*                   type="string"
*               ),
*               @OA\Property(
*                   property="firstname",
*                   description="firstname",
*                   type="string"
*               ),
*               @OA\Property(
*                   property="lastname",
*                   description="lastname",
*                   type="string"
*               ),
*               @OA\Property(
*                   property="email",
*                   description="email",
*                   type="string"
*               ),
*               @OA\Property(
*                   property="email_verified_at",
*                   description="email_verified_at",
*                   type="string"
*               ),
*               @OA\Property(
*                   property="password",
*                   description="password",
*                   type="string"
*               ),
*               @OA\Property(
*                   property="status_id",
*                   description="status_id",
*                   type="string"
*               ),
*               @OA\Property(
*                   property="current_organization_id",
*                   description="current_organization_id",
*                   type="string"
*               ),
*               @OA\Property(
*                   property="current_period_id",
*                   description="current_period_id",
*                   type="string"
*               ) 
*           )
*       )
*   ),
*      
*       @OA\Response(
*          response=200,
*          description="successful operation",
*          @OA\JsonContent(ref="#/components/schemas/User")   
*       ),
*       @OA\Response(response=400, description="Bad request"),
*       security={
*           {"api_key_security_example": {}}
*       }
*      
* )
* 
*/


/**
* @OA\Get(
*      path="/v1/users/{id}",
*      operationId="users",
*      tags={"User v1"},
*      summary="Get all users",
*      description="Returns a collection of user objects",
*      security={
*           {"passport": {"*"}},
*      },
*      @OA\Parameter(
*          name="id",
*          description="User id",
*          required=true,
*          in="path",
*          @OA\Schema(
*              type="integer"
*          )
*      ),
*      @OA\Response(
*          response=200,
*          description="successful operation",
*          @OA\JsonContent(ref="#/components/schemas/User"),
*       ),
*       @OA\Response(response=400, description="Bad request"),
* )
*
*/

/**
* @OA\DELETE(
*      path="/v1/users/{id}",
*      operationId="users",
*      tags={"User v1"},
*      summary="Delete user by Id",
*      description="Delete a user object",
*      security={
*           {"passport": {"*"}},
*      },
*      @OA\Parameter(
*          name="id",
*          description="User id",
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
* 
*/

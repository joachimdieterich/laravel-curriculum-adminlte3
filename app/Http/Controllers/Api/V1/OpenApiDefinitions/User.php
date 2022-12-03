<?php
namespace App\Http\Controllers\Api\V1\OpenApiDefinitions;

/**
* @OA\Get(
*      path="/v1/users?common_name={common_name}",
*      operationId="getAllUsers",
*      tags={"User v1"},
*      summary="Get all users",
*      description="Returns a collection of user objects",
*      security={
*           {"passport": {"*"}},
*      },
 *     @OA\Parameter(
 *          name="common_name",
 *          description="common_name",
 *          required=false,
 *          in="path",
 *          @OA\Schema(
 *              type="string"
 *          )
 *      ),
*      @OA\Response(
*          response=200,
*          description="successful operation",
*          @OA\Schema(ref="#/components/schemas/Users"),
*       ),
*       @OA\Response(response=400, description="Bad request"),
* )
*
* @OA\Post(
*      path="/v1/users",
*      operationId="createUser",
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
*               required={"common_name", "username", "firstname", "lastname", "email", "password"},
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
*
* )
*
* @OA\Put(
*      path="/v1/users/{user}",
*      operationId="updateUser",
*      tags={"User v1"},
*      summary="Edit user",
*      description="Edit user",
*      security={
*           {"passport": {"*"}},
*      },
*      @OA\Parameter(
*          name="user",
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
*
* )
*
* @OA\Get(
*      path="/v1/users/{user}",
*      operationId="getUser",
*      tags={"User v1"},
*      summary="Get all users",
*      description="Returns a collection of user objects",
*      security={
*           {"passport": {"*"}},
*      },
*      @OA\Parameter(
*          name="user",
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
* @OA\Delete(
*      path="/v1/users/{user}",
*      operationId="deleteUser",
*      tags={"User v1"},
*      summary="(Soft)-Delete user by Id",
*      description="(Soft)-Delete a user object",
*      security={
*           {"passport": {"*"}},
*      },
*      @OA\Parameter(
*          name="user",
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
 * @OA\Delete(
 *      path="/v1/users/{user}/force",
 *      operationId="forceDeleteUser",
 *      tags={"User v1"},
 *      summary="(Force)-Delete user by Id",
 *      description="(Force)-Delete a user object",
 *      security={
 *           {"passport": {"*"}},
 *      },
 *      @OA\Parameter(
 *          name="user",
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
* @OA\Get(
*      path="/v1/users/{user}/dashboard",
*      operationId="getUsersDashboard",
*      tags={"User v1"},
*      summary="Get dashboard of user",
*      description="Returns groups in which user is enrolled with related curricula, notifications and events",
*      security={
*           {"passport": {"*"}},
*      },
*      @OA\Parameter(
*          name="user",
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
*       ),
*       @OA\Response(response=400, description="Bad request"),
* )
*
* @OA\Get(
*      path="/v1/users/{user}/groups",
*      operationId="getUsersWithGroups",
*      tags={"User v1"},
*      summary="Get user with groups",
*      description="Returns user with groups",
*      security={
*           {"passport": {"*"}},
*      },
*      @OA\Parameter(
*          name="user",
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
*       ),
*       @OA\Response(response=400, description="Bad request"),
* )
*
* @OA\Get(
*      path="/v1/users/{user}/organizations",
*      operationId="getUsersWithOrganizations",
*      tags={"User v1"},
*      summary="Get user with organizations",
*      description="Returns user with organizations",
*      security={
*           {"passport": {"*"}},
*      },
*      @OA\Parameter(
*          name="user",
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
*       ),
*       @OA\Response(response=400, description="Bad request"),
* )
*
* @OA\Get(
*      path="/v1/users/{user}/roles",
*      operationId="getUsersWithRoles",
*      tags={"User v1"},
*      summary="Get user with roles",
*      description="Returns user with roles",
*      security={
*           {"passport": {"*"}},
*      },
*      @OA\Parameter(
*          name="user",
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
*       ),
*       @OA\Response(response=400, description="Bad request"),
* )
*/

class User
{
}

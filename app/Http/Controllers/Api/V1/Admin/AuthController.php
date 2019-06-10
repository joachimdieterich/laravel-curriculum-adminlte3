<?php
/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Curriculum OpenApi",
 *      description="Curriculum OpenApi description",
 *      @OA\Contact(
 *          email="admin@curriculumonline.de"
 *      ),
 *     @OA\License(
 *         name="Apache 2.0",
 *         url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *     )
 * )
 */

/**
 *
 *  @OA\Server(
 *      url="https://127.0.0.1:8000/api/",
 *      description="Curriculum OpenApi Server"
 * )
 */

/**
 * @OA\SecurityScheme(
 *     type="oauth2",
 *     description="Use a global client_id / client_secret and your username / password combo to obtain a token",
 *     name="Password Based",
 *     in="header",
 *     scheme="https",
 *     securityScheme="Password Based",
 *     @OA\Flow(
 *         flow="password",
 *         authorizationUrl="/oauth/authorize",
 *         tokenUrl="/oauth/token",
 *         refreshUrl="/oauth/token/refresh",
 *         scopes={}
 *     )
 * )
 */
namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\User;

class AuthController extends Controller
{
    /**
    * @OA\POST(
    *      path="/v1/signup",
    *      operationId="signup",
    *      tags={"Auth"},
    *      summary="Sign up a user",
    *      description="Sign up a user",
    *      @OA\Parameter(
    *          name="name",
    *          description="User name",
    *          required=true,
    *          in="path",
    *          @OA\Schema(
    *              type="string"
    *          )
    *      ),
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

    *      @OA\Response(
    *          response=200,
    *          description="successful operation"
    *       ),
    *       @OA\Response(response=400, description="Bad request"),
    *       security={
    *           {"api_key_security_example": {}}
    *       }
    *     )
    *
    * Returns message
    */
    /**
     * Create user
     *
     * @param  [string] name
     * @param  [string] email
     * @param  [string] password
     * @param  [string] password_confirmation
     * @return [string] message
     */
    public function signup(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|confirmed'
        ]);
        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);
        $user->save();
        return response()->json([
            'message' => 'Successfully created user!'
        ], 201);
    }
  
    /**
    * @OA\Post(
    *      path="/v1/login",
    *      operationId="login",
    *      tags={"Auth"},
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
    *          name="remamber_me",
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
    *       security={
    *           {"api_key_security_example": {}}
    *       }
    *     )
    *
    * Returns strings: access_token, token_type, expires at
    */
    /**
     * Login user and create token
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [boolean] remember_me
     * @return [string] access_token
     * @return [string] token_type
     * @return [string] expires_at
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'remember_me' => 'boolean'
        ]);
        $credentials = request(['email', 'password']);
        if(!Auth::attempt($credentials))
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        if ($request->remember_me)
            $token->expires_at = Carbon::now()->addWeeks(1);
        $token->save();
        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString()
        ]);
    }
  
    
    /**
    * @OA\Get(
    *      path="/v1/logout",
    *      operationId="logout",
    *      tags={"Auth"},
    *      summary="Logout a user",
    *      description="Logout a user",
    *     
    *      @OA\Response(
    *          response=200,
    *          description="successful operation"
    *       ),
    *       @OA\Response(response=400, description="Bad request"),
    *       security={
    *           {"api_key_security_example": {}}
    *       }
    *     )
    *
    * Returns message
    */
    /**
     * Logout user (Revoke the token)
     *
     * @return [string] message
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }
  
    /**
    * @OA\Get(
    *      path="/v1/user",
    *      operationId="user",
    *      tags={"Auth"},
    *      summary="Get the authenticated User",
    *      description="Returns a user object",
    *     
    *      @OA\Response(
    *          response=200,
    *          description="successful operation"
    *       ),
    *       @OA\Response(response=400, description="Bad request"),
    *       security={
    *           {"api_key_security_example": {}}
    *       }
    *     )
    *
    * Returns user object
    */
    /**
     * Get the authenticated User
     *
     * @return [json] user object
     */
    public function user(Request $request)
    {
        return response()->json($request->user());
    }
}
<?php

namespace App\Http\Controllers\API\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
 /**
   * @OA\Tag(
   *     name="About",
   *     description="Operations about",
   *     @OA\ExternalDocumentation(
   *         description="Find out more about",
   *         url="http://swagger.io"
   *     )
   * )
   *   
   */
class AboutApiController extends Controller
{
    
   /**
    * @OA\Get(
    *      path="/v1/about",
    *      operationId="about",
    *      tags={"About"},
    *      summary="Get about",
    *      description="Returns About",
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
    * Returns list of projects
    */
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return "Curriculum API (about) works";
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

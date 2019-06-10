<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrganizationRequest;
use App\Http\Requests\UpdateOrganizationRequest;
use App\Organization;

class OrganizationsApiController extends Controller
{
    /**
    * @OA\Get(
    *      path="/v1/organizations",
    *      operationId="organizations",
    *      tags={"Organizations"},
    *      summary="Get all organizations",
    *      description="Returns a collection of organization objects",
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
    * Returns list of Organizations
    */
    public function index()
    {
        $organizations = Organization::all();

        return $organizations;
    }

    public function store(StoreOrganizationRequest $request)
    {
        return Organization::create($request->all());
    }

    public function update(UpdateOrganizationRequest $request, Organization $product)
    {
        return $product->update($request->all());
    }

    public function show(Organization $product)
    {
        return $product;
    }

    public function destroy(Organization $product)
    {
        return $product->delete();
    }
}

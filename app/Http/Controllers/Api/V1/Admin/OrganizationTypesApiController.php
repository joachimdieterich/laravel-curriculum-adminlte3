<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\OrganizationType;

class OrganizationTypesApiController extends Controller {

    public function index() {
        return OrganizationType::all();
    }

    public function show(OrganizationType $organizationtype) {
        return $organizationtype;
    }

}

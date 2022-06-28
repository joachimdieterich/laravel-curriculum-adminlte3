<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Role;

class RolesApiController extends Controller
{
    public function index()
    {
        $roles = Role::all();

        return $roles;
    }

    public function show(Role $role)
    {
        return $role;
    }
}

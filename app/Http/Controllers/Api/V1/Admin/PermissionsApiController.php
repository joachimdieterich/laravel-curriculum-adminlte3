<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;
use App\Permission;

class PermissionsApiController extends Controller
{
    public function index()
    {
        return Permission::all();
    }

    public function show(Permission $permission)
    {
        return $permission;
    }
}

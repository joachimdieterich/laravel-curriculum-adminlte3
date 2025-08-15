<?php

namespace App\Http\Controllers;

use App\Http\Requests\MassDestroyPermissionRequest;
use App\Http\Requests\StorePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;
use App\Permission;
use Yajra\DataTables\DataTables;

class PermissionsController extends Controller
{
    public function index()
    {
        abort_unless(\Gate::allows('permission_access'), 403);
        if (request()->wantsJson()) {
            return  getEntriesForSelect2ByModel(
                "App\Permission"
            );
        }
        return view('permissions.index');
    }

    public function list()
    {
        abort_unless(\Gate::allows('permission_access'), 403);

        $permissions = Permission::all();

        return DataTables::of($permissions)
            ->addColumn('check', '')
            ->setRowId('id')
            ->make(true);
    }

    public function store(StorePermissionRequest $request)
    {
        abort_unless(\Gate::allows('permission_create'), 403);

        $permission = Permission::create($request->all());

        if (request()->wantsJson()) {
            return $permission;
        }
    }

    public function update(UpdatePermissionRequest $request, Permission $permission)
    {
        abort_unless(\Gate::allows('permission_edit'), 403);

        $permission->update($request->all());

        if (request()->wantsJson()) {
            return $permission;
        }
    }

    public function show(Permission $permission)
    {
        abort_unless(\Gate::allows('permission_show'), 403);

        return view('permissions.show')
            ->with(compact('permission'));
    }

    public function destroy(Permission $permission)
    {
        abort_unless(\Gate::allows('permission_delete'), 403);

        $return = $permission->delete();

        if (request()->wantsJson()) {
            return ['message' => $return];
        }
    }

    public function massDestroy(MassDestroyPermissionRequest $request)
    {
        Permission::whereIn('id', request('ids'))->delete();

        return response(null, 204);
    }
}

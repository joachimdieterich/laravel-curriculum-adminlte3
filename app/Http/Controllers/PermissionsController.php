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

        return view('permissions.index');
    }

    public function list()
    {
        abort_unless(\Gate::allows('permission_access'), 403);

        $permissions = Permission::all();

        $edit_gate = \Gate::allows('permission_edit');
        $delete_gate = \Gate::allows('permission_delete');

        return DataTables::of($permissions)

            ->addColumn('action', function ($permissions) use ($edit_gate, $delete_gate) {
                $actions = '';
                if ($edit_gate) {
                    $actions .= '<a href="'.route('permissions.edit', $permissions->id).'" '
                                    .'id="permission-user-'.$permissions->id.'" '
                                    .'class="btn">'
                                    .'<i class="fa fa-pencil-alt"></i>'
                                    .'</a>';
                }
                if ($delete_gate) {
                    $actions .= '<button type="button" '
                                .'class="btn text-danger" '
                                .'onclick="destroyDataTableEntry(\'permissions\','.$permissions->id.')">'
                                .'<i class="fa fa-trash"></i></button>';
                }

                return $actions;
            })

            ->addColumn('check', '')
            ->setRowId('id')
            ->make(true);
    }

    public function create()
    {
        abort_unless(\Gate::allows('permission_create'), 403);

        return view('permissions.create');
    }

    public function store(StorePermissionRequest $request)
    {
        abort_unless(\Gate::allows('permission_create'), 403);

        $permission = Permission::create($request->all());

        return redirect()->route('permissions.index');
    }

    public function edit(Permission $permission)
    {
        abort_unless(\Gate::allows('permission_edit'), 403);

        return view('permissions.edit', compact('permission'));
    }

    public function update(UpdatePermissionRequest $request, Permission $permission)
    {
        abort_unless(\Gate::allows('permission_edit'), 403);

        $permission->update($request->all());

        return redirect()->route('permissions.index');
    }

    public function show(Permission $permission)
    {
        abort_unless(\Gate::allows('permission_show'), 403);

        return view('permissions.show', compact('permission'));
    }

    public function destroy(Permission $permission)
    {
        abort_unless(\Gate::allows('permission_delete'), 403);

        $permission->delete();

        return back();
    }

    public function massDestroy(MassDestroyPermissionRequest $request)
    {
        Permission::whereIn('id', request('ids'))->delete();

        return response(null, 204);
    }
}

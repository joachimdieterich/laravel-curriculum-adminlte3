<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Permission;
use App\Role;
use Illuminate\Support\Facades\Cache;
use Yajra\DataTables\DataTables;

class RolesController extends Controller
{
    public function index()
    {
        abort_unless(\Gate::allows('role_access'), 403);

        if (request()->wantsJson()) {
            if (is_admin()) {
                return getEntriesForSelect2ByModel("App\Role");
            } else {
                return getEntriesForSelect2ByCollection(
                    Role::where('id', '>', auth()->user()->role()->id)
                );
            }
        }

        return view('roles.index');
    }

    public function list()
    {
        abort_unless(\Gate::allows('role_access'), 403);
        $roles = Role::select([
            'id',
            'title',
        ]);

        return DataTables::of($roles)
            ->addColumn('permissions', function ($roles) {
                return $roles->permissions->pluck('id')->toArray();
            })
            ->addColumn('tags', function ($roles) {
                return $roles->tags->toArray();
            })
            ->addColumn('check', '')
            ->setRowId('id')
            ->setRowAttr([
                'color' => 'primary',
            ])
            ->make(true);
    }

    public function store(StoreRoleRequest $request)
    {
        abort_unless(\Gate::allows('role_create'), 403);

        $role = Role::create($request->all());
        $role->permissions()->sync($request->input('permissions', []));
        $role->tags()->sync($request->input('tags', []));

        Cache::forget('roles'); //cache should update next time

        if (request()->wantsJson()) {
            return $role;
        }
    }


    public function update(UpdateRoleRequest $request, Role $role)
    {
        abort_unless(\Gate::allows('role_edit'), 403);

        $role->update($request->all());
        $role->permissions()->sync($request->input('permissions', []));
        $role->tags()->sync($request->input('tags', []));

        Cache::forget('roles'); //cache should update next time

        if (request()->wantsJson()) {
            return $role;
        }
    }

    public function show(Role $role)
    {
        abort_unless(\Gate::allows('role_show'), 403);

        $role->load('permissions');
        $role->load('tags');

        return view('roles.show')
            ->with(compact('role'));
    }

    public function destroy(Role $role)
    {
        abort_unless(\Gate::allows('role_delete'), 403);

        $return = $role->delete();

        if (request()->wantsJson()) {
            return ['message' => $return];
        }
    }
}

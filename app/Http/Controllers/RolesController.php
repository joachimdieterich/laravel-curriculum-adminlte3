<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyRoleRequest;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Permission;
use App\Role;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Cache;

class RolesController extends Controller
{
    public function index()
    {
        abort_unless(\Gate::allows('role_access'), 403);

        return view('roles.index');
    }

    public function list()
    {
        abort_unless(\Gate::allows('role_access'), 403);
        $roles = Role::select([
            'id', 
            'title'
            ]);
        
        return DataTables::of($roles)
            
            ->addColumn('action', function ($roles) {
                 $actions  = '';
                    if (\Gate::allows('role_edit')){
                        $actions .= '<a href="'.route('roles.edit', $roles->id).'" '
                                    . 'class="btn">'
                                    . '<i class="fa fa-pencil-alt"></i>'
                                    . '</a>';
                    }
                    if (\Gate::allows('role_delete')){
                        $actions .= '<button type="button" '
                                . 'class="btn text-danger" '
                                . 'onclick="destroyDataTableEntry(\'roles\','.$roles->id.')">'
                                . '<i class="fa fa-trash"></i></button>';
                    }
                return $actions;
            })
           
            ->addColumn('check', '')
            ->setRowId('id')
            ->setRowAttr([
                'color' => 'primary',
            ])
            ->make(true);
    }
    
    public function create()
    {
        abort_unless(\Gate::allows('role_create'), 403);

        $permissions = Permission::all()->pluck('title', 'id');

        return view('roles.create', compact('permissions'));
    }
   
    public function store(StoreRoleRequest $request)
    {
        abort_unless(\Gate::allows('role_create'), 403);

        $role = Role::create($request->all());
        $role->permissions()->sync($request->input('permissions', []));

        Cache::forget('roles'); //cache should update next time
            
        return redirect()->route('roles.index');
    }

    public function edit(Role $role)
    {
        abort_unless(\Gate::allows('role_edit'), 403);

        $permissions = Permission::all()->pluck('title', 'id');

        $role->load('permissions');

        return view('roles.edit', compact('permissions', 'role'));
    }

    public function update(UpdateRoleRequest $request, Role $role)
    {
        abort_unless(\Gate::allows('role_edit'), 403);

        $role->update($request->all());
        $role->permissions()->sync($request->input('permissions', []));
        
        Cache::forget('roles'); //cache should update next time

        return redirect()->route('roles.index');
    }

    public function show(Role $role)
    {
        abort_unless(\Gate::allows('role_show'), 403);

        $role->load('permissions');

        return view('roles.show', compact('role'));
    }

    public function destroy(Role $role)
    {
        abort_unless(\Gate::allows('role_delete'), 403);

        $role->delete();

        return back();
    }

    public function massDestroy(MassDestroyRoleRequest $request)
    {
        Role::whereIn('id', request('ids'))->delete();

        return response(null, 204);
    }
}

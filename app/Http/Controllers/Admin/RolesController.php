<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyRoleRequest;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Permission;
use App\Role;
use Yajra\DataTables\DataTables;

class RolesController extends Controller
{
    public function index()
    {
        abort_unless(\Gate::allows('role_access'), 403);

        //$roles = Role::all();

        return view('admin.roles.index');
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
                    if (\Gate::allows('role_show')){
                        $actions .= '<a href="'.route('admin.roles.show', $roles->id).'" '
                                    . 'class="btn btn-xs btn-success mr-1">'
                                    . '<i class="fa fa-list-alt"></i> Show'
                                    . '</a>';
                    }
                    if (\Gate::allows('role_edit')){
                        $actions .= '<a href="'.route('admin.roles.edit', $roles->id).'" '
                                    . 'class="btn btn-xs btn-primary mr-1">'
                                    . '<i class="fa fa-edit"></i> Edit'
                                    . '</a>';
                    }
                    if (\Gate::allows('role_delete')){
                        $actions .= '<form action="'.route('admin.roles.destroy', $roles->id).'" method="POST">'
                                    . '<input type="hidden" name="_method" value="delete">'. csrf_field().''
                                    . '<button type="submit" class="btn btn-xs btn-danger"><i class="fa fa-trash"></i> Delete</button>';
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

        return view('admin.roles.create', compact('permissions'));
    }
   
    public function store(StoreRoleRequest $request)
    {
        abort_unless(\Gate::allows('role_create'), 403);

        $role = Role::create($request->all());
        $role->permissions()->sync($request->input('permissions', []));

        return redirect()->route('admin.roles.index');
    }

    public function edit(Role $role)
    {
        abort_unless(\Gate::allows('role_edit'), 403);

        $permissions = Permission::all()->pluck('title', 'id');

        $role->load('permissions');

        return view('admin.roles.edit', compact('permissions', 'role'));
    }

    public function update(UpdateRoleRequest $request, Role $role)
    {
        abort_unless(\Gate::allows('role_edit'), 403);

        $role->update($request->all());
        $role->permissions()->sync($request->input('permissions', []));

        return redirect()->route('admin.roles.index');
    }

    public function show(Role $role)
    {
        abort_unless(\Gate::allows('role_show'), 403);

        $role->load('permissions');

        return view('admin.roles.show', compact('role'));
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

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyUserRequest;
use App\Http\Requests\MassUpdateUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Role;
use App\User;
use App\Organization;
use App\OrganizationRoleUser;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Request;
use Yajra\DataTables\DataTables;

class UsersController extends Controller
{
    public function index()
    {
        abort_unless(\Gate::allows('user_access'), 403);

        $users = User::all();
        $organizations = Organization::all();

        return view('admin.users.index', [
            'users' => $users,
            'organizations' => $organizations,
            
        ]);
    }
    
    public function userList()
    {
        $users = User::select([
            'id', 
            'username', 
            'firstname', 
            'lastname', 
            'email',
            'email_verified_at'
            ]);
        
        return DataTables::of($users)
            ->addColumn('action', function ($users) {
                 $actions  = '';
                    if (\Gate::allows('user_show')){
                        $actions .= '<a href="'.route('admin.users.show', $users->id).'" '
                                    . 'class="btn btn-xs btn-success">'
                                    . '<i class="fa fa-list-alt"></i> Show'
                                    . '</a>';
                    }
                    if (\Gate::allows('user_edit')){
                        $actions .= '<a href="'.route('admin.users.edit', $users->id).'" '
                                    . 'class="btn btn-xs btn-primary">'
                                    . '<i class="fa fa-edit"></i> Edit'
                                    . '</a>';
                    }
                    if (\Gate::allows('user_delete')){
                        $actions .= '<form action="'.route('admin.users.destroy', $users->id).'" method="POST">'
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
        abort_unless(\Gate::allows('user_create'), 403);

        $roles = Role::all()->pluck('title', 'id');

        return view('admin.users.create', compact('roles'));
    }

    public function store(StoreUserRequest $request)
    {
        abort_unless(\Gate::allows('user_create'), 403);

        $user = User::create($request->all());
        
        //$user->roles()->sync($request->input('roles', []));

        return redirect()->route('admin.users.index');
    }

    public function edit(User $user)
    {
        abort_unless(\Gate::allows('user_edit'), 403);

        $roles = Role::all()->pluck('title', 'id');

        $user->load('roles');

        return view('admin.users.edit', compact('roles', 'user'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        abort_unless(\Gate::allows('user_edit'), 403);

        $user->update($request->all());
        $user->roles()->sync($request->input('roles', []));

        return redirect()->route('admin.users.index');
    }
    
    public function massUpdate(MassUpdateUserRequest $request)
    {
        //dd(request());
        
        User::whereIn('id', request('ids'))->update([
            'password' => Hash::make($request->password)
                ]);  
        
        return response(null, 204);
    }

    public function show(User $user)
    {
        abort_unless(\Gate::allows('user_show'), 403);

        $user->load('roles');

        return view('admin.users.show', compact('user'));
    }

    public function destroy(User $user)
    {
        abort_unless(\Gate::allows('user_delete'), 403);

        $user->delete();

        return back();
    }

    public function massDestroy(MassDestroyUserRequest $request)
    {
        User::whereIn('id', request('ids'))->delete();

        return response(null, 204);
    }
    
    
    public function enrolToOrganization(User $user, Request $request)
    { 
        
        abort_unless(\Gate::allows('user_enrol'), 403);
        
        foreach ( $request->organizations AS $organization_id)
        {
           auth()->user()->enrol($user->id, $organization_id, $request->role_id);
        }
        return redirect()->route('admin.users.index');
    }
    
    public function expelFromOrganization(User $user, Organization $organization)
    {
        abort_unless(\Gate::allows('user_expel'), 403);
        
        auth()->user()->expel($user->id, $organization->id);

        return redirect()->route('admin.users.index');
    }
    
 
}

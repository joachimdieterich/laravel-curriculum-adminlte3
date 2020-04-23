<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyUserRequest;
use App\Http\Requests\MassUpdateUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Role;
use App\User;
use App\Organization;
use App\Group;
use App\StatusDefinition;
use App\Medium;
use App\OrganizationRoleUser;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Request;
use Yajra\DataTables\DataTables;

class UsersController extends Controller
{
    public function index()
    {
        abort_unless(\Gate::allows('user_access'), 403);
 
        if (auth()->user()->role()->id == 1)
        {
            $organizations = Organization::all(); 
            $roles = Role::all();
            $groups = Group::all();
        } 
        else 
        {
            $organizations = auth()->user()->organizations()->get(); 
            $roles = Role::where('id', '>',  auth()->user()->role()->id)->get();
            $groups = auth()->user()->groups()->orderBy('organization_id', 'desc')->get();
        }
        $status_definitions = StatusDefinition::all();
        
        return view('users.index')
          //>with(compact('users'))
          ->with(compact('organizations'))
          ->with(compact('status_definitions'))
          ->with(compact('groups'))
          ->with(compact('roles'));
    }
    
    public function list()
    {
        $users = (auth()->user()->role()->id == 1) ? User::all() : Organization::where('id', auth()->user()->current_organization_id)->get()->first()->users()->get();
        
        return DataTables::of($users)
            ->addColumn('status', function ($users) {
                return $users->status()->first()->lang_de;                
            })
            ->addColumn('action', function ($users) {
                 $actions  = '';
                    if (\Gate::allows('user_show')){
                        $actions .= '<a href="'.route('users.show', $users->id).'" '
                                    . 'id="show-user-'.$users->id.'" '
                                    . 'class="btn btn-xs btn-success mr-1">'
                                    . '<i class="fa fa-list-alt"></i>'
                                    . '</a>';
                    }
                    if (\Gate::allows('user_edit')){
                        $actions .= '<a href="'.route('users.edit', $users->id).'" '
                                    . 'id="edit-user-'.$users->id.'" '
                                    . 'class="btn btn-xs btn-primary  mr-1">'
                                    . '<i class="fa fa-edit"></i>'
                                    . '</a>';
                    }
                    if (\Gate::allows('user_delete')){
                        $actions .= '<form action="'.route('users.destroy', $users->id).'" method="POST" class="float-right">'
                                    . '<input type="hidden" name="_method" value="delete">'. csrf_field().''
                                    . '<button '
                                    . 'type="submit" ' 
                                    . 'id="delete-user-'.$users->id.'" '
                                    . 'class="btn btn-xs btn-danger"> <i class="fa fa-trash"></i></button></form>';
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

        return view('users.create');
    }

    public function store(StoreUserRequest $request)
    {
        abort_unless(\Gate::allows('user_create'), 403);

        $user = User::create($request->all());
        
        /*
         * Todo User have to be enroled to (creators) institution 
         */
        OrganizationRoleUser::firstOrCreate(
            [
                'user_id'         => $user->id,
                'organization_id' => auth()->user()->current_organization_id,
            ],
            [
                'role_id'         => 6 //student
            ]
        );
        
        //$user->roles()->sync($request->input('roles', []));
         return redirect($user->path());
    }

    public function edit(User $user)
    {
        abort_unless(\Gate::allows('user_edit'), 403);

        $roles = Role::all()->pluck('title', 'id');

        $user->load('roles');

        return view('users.edit', compact('roles', 'user'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        abort_unless(\Gate::allows('user_edit'), 403);

        $user->update($request->all());
        //$user->roles()->sync($request->input('roles', []));

        return redirect()->route('users.index');
    }
    
    public function massUpdate(MassUpdateUserRequest $request)
    {
        //dd(request());
        if (isset(request()->password[0]))
        {
            User::whereIn('id', request('ids'))->update([
            'password' => Hash::make($request->password)
                ]); 
        }
        
        if (isset(request()->status_id[0]))
        {
        User::whereIn('id', request('ids'))->update([
            'status_id' => $request->status_id
                ]);  
        }
        
        return response(null, 204);
    }

    public function show(User $user)
    {
        abort_unless(\Gate::allows('user_show'), 403);
        $status_definitions = StatusDefinition::all();
        $user->load('roles');
        $user->load('organizations');

        return view('users.show')
                ->with(compact('user'))
                ->with(compact('status_definitions'));
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
    public function setCurrentOrganization()
    {
        User::where('id', auth()->user()->id)->update([
            'current_organization_id' => request('current_organization_id')
                ]); 
        
        return back();
    }
   
    /**
     * Set users Profile Image
     * @return type
     */
    public function setAvatar()
    {
        $medium = new Medium();
        dump(request());
        User::where('id', auth()->user()->id)->update([
            'medium_id' => (null !== $medium->getByFilemanagerPath(request('filepath'))) ? $medium->getByFilemanagerPath(request('filepath'))->id : null,
                ]); 
        
        return back();
    }
 
}

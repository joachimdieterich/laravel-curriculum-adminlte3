<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyUserRequest;
use App\Http\Requests\MassUpdateUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Role;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Organization;
use App\Group;
use App\StatusDefinition;
use App\Medium;
use App\OrganizationRoleUser;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Request;
use Yajra\DataTables\DataTables;
use App\Imports\UsersImport;
use Maatwebsite\Excel\Facades\Excel;

class UsersController extends Controller
{
    public function index()
    {
        abort_unless(\Gate::allows('user_access'), 403);

        if (auth()->user()->role()->id == 1)
        {
            $organizations = Organization::all();
            $roles = Role::all();
            $groups = Group::orderBy('organization_id', 'desc')->get() ;
        }
        else
        {
            $organizations = auth()->user()->organizations()->get();
            $roles = Role::where('id', '>',  auth()->user()->role()->id)->get();
            $groups = (auth()->user()->role()->id == 4) ? Group::where('organization_id', auth()->user()->current_organization_id)->get() : auth()->user()->groups()->orderBy('organization_id', 'desc')->get();
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
        $users = (auth()->user()->role()->id == 1) ? DB::table('users')->select('id', 'username', 'firstname', 'lastname', 'email') : Organization::where('id', auth()->user()->current_organization_id)->get()->first()->users();

        $show_gate = \Gate::allows('user_show');
        $edit_gate = \Gate::allows('user_edit');
        $delete_gate = \Gate::allows('user_delete');

        return DataTables::of($users)
            ->addColumn('action', function ($users) use ($show_gate, $edit_gate, $delete_gate) {
                 $actions  = '';
                    if ($show_gate){
                        $actions .= '<a href="'.route('users.show', $users->id).'" '
                                    . 'id="show-user-'.$users->id.'" '
                                    . 'class="btn">'
                                    . '<i class="fa fa-list-alt"></i>'
                                    . '</a>';
                    }
                    if ($edit_gate){
                        $actions .= '<a href="'.route('users.edit', $users->id).'" '
                                    . 'id="edit-user-'.$users->id.'" '
                                    . 'class="btn">'
                                    . '<i class="fa fa-pencil-alt"></i>'
                                    . '</a>';
                    }
                    if ($delete_gate){
                        $actions .= '<button type="button" '
                                . 'class="btn text-danger" '
                                . 'onclick="destroyDataTableEntry(\'users\','.$users->id.')">'
                                . '<i class="fa fa-trash"></i></button>';
                    }

                return $actions;
            })

            ->addColumn('check', '')
            ->setRowId('id')
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

        if (User::withTrashed()->where('email', request()->email)->exists())
        {
            User::withTrashed()->where('email', request()->email)->restore();
            $user = User::where('email', request()->email)->get()->first();
            $user->update($request->all());
        }
        else
        {
            $user = User::create($request->all());
        }


        /*
         * Enrol user to (creators) institution. Every user have to be enrolled to an institution!
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
        $user->current_organization_id = auth()->user()->current_organization_id; //set default org
        $user->save();

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

        $return = $user->delete();
        //todo concept to hard-delete users
        if (request()->wantsJson()){
            return ['message' => $return];
        }
    }

    public function massDestroy(MassDestroyUserRequest $request)
    {
        User::whereIn('id', request('ids'))->delete();

        return response(null, 204);
    }

    public function setCurrentOrganization()
    {
        User::where('id', auth()->user()->id)->update([
            'current_period_id' => (request('current_period_id')) ? request('current_period_id') : 1,
            'current_organization_id' => request('current_organization_id')
        ]);

        return back();
    }
    public function setCurrentPeriod()
    {
        User::where('id', auth()->user()->id)->update([
            'current_period_id' => request('current_period_id')
        ]);

        return back();
    }

    /**
     * Set users Profile Image
     * @return type
     */
    public function setAvatar()
    {
        if (null !== request('medium_id'))
        {
            $medium = Medium::find(request('medium_id'));
            $medium->public = 1;
            $medium->save();

            User::where('id', auth()->user()->id)->update([
                'medium_id' => $medium->id,
                ]);
        }

        return back();
    }

    public function getAvatar(User $user)
    {
        if (request()->wantsJson()){
            return ['avatar' => ($user->medium_id !== null) ? '/media/'.$user->medium_id  : (new \Laravolt\Avatar\Avatar)->create($user->fullName())->toBase64()];
        } else {
            return ($user->medium_id !== null) ? '/media/'.$user->medium_id  : (new \Laravolt\Avatar\Avatar)->create($user->fullName())->toBase64();
        }

    }

    public function createImport()
    {
        abort_unless(\Gate::allows('user_create'), 403);

        return view('users.import');
    }

    public function storeImport(Request $request)
    {
        abort_unless(\Gate::allows('user_create'), 403);

        $import = $this->validateImportRequest($request);

        $medium = Medium::find($import['medium_id']);
        Excel::import(new UsersImport($request), storage_path('app'.$medium->path.$medium->medium_name));

        return view('users.import');
    }

    protected function validateImportRequest()
    {
        return request()->validate(
            [
                'medium_id'            => 'required',
            ]
        );
    }
}

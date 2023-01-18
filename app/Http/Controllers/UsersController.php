<?php

namespace App\Http\Controllers;

use App\Http\Requests\MassDestroyUserRequest;
use App\Http\Requests\MassUpdateUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Imports\UsersImport;
use App\Medium;
use App\Organization;
use App\OrganizationRoleUser;
use App\Role;
use App\Scopes\NoSharingUsers;
use App\StatusDefinition;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\Request;
use Yajra\DataTables\DataTables;

class UsersController extends Controller
{
    public function index()
    {
        abort_unless(\Gate::allows('user_access'), 403);

        if (request()->wantsJson() and request()->has(['term', 'page'])) {
            return  getEntriesForSelect2ByCollection(
                Organization::where('id', auth()->user()->current_organization_id)->get()->first()->users()->noSharing(),
                'users.',
                ['username', 'firstname', 'lastname'],
                'lastname',
                "CONCAT(firstname, ' ' ,lastname)",
            );
        }
        // todo check: is the following condition used anymore
        if (request()->wantsJson()) {
            if (auth()->user()->role()->id == 1) {
                $users = User::addGlobalScope(NoSharingUsers::class)->withTrashed()
                    ->select('id', 'username', 'firstname', 'lastname', 'email', 'deleted_at')->get();

                return ['users' => $users];
            } else {
                return ['users' => json_encode(Organization::where('id', auth()->user()->current_organization_id)
                    ->get()->first()->users()->addGlobalScope(NoSharingUsers::class)->get())];
            }
        }

        return view('users.index');
    }

    public function list()
    {
        $users = (auth()->user()->role()->id == 1)
            ? User::noSharing()->select('id', 'username', 'firstname', 'lastname', 'email', 'deleted_at')
            : Organization::where('id', auth()->user()->current_organization_id)->get()->first()->users()->noSharing();

        $show_gate = \Gate::allows('user_show');
        $edit_gate = \Gate::allows('user_edit');
        $delete_gate = \Gate::allows('user_delete');

        return DataTables::of($users)
            ->addColumn('action', function ($users) use ($show_gate, $edit_gate, $delete_gate) {
                $actions = '';
                if ($show_gate) {
                    $actions .= '<a href="'.route('users.show', $users->id).'" '
                                    .'id="show-user-'.$users->id.'" '
                                    .'class="btn">'
                                    .'<i class="fa fa-list-alt"></i>'
                                    .'</a>';
                }
                if ($edit_gate) {
                    $actions .= '<a href="'.route('users.edit', $users->id).'" '
                                    .'id="edit-user-'.$users->id.'" '
                                    .'class="btn">'
                                    .'<i class="fa fa-pencil-alt"></i>'
                                    .'</a>';
                }
                if ($delete_gate) {
                    $actions .= '<button type="button" '
                                .'class="btn text-danger" '
                                .'onclick="destroyDataTableEntry(\'users\','.$users->id.')">'
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
        abort_unless(\Gate::allows('user_create'), 403);

        return view('users.create');
    }

    public function store(StoreUserRequest $request)
    {
        abort_unless(\Gate::allows('user_create'), 403);
        //todo: users should only be created in accessible organizations/roles
        //
        if (User::withTrashed()->where('email', request()->email)->exists()) {
            User::withTrashed()->where('email', request()->email)->restore();
            $user = User::where('email', request()->email)->get()->first();
            $user->update($request->all());
        } else {
            $user = User::create($request->all());
        }

        /*
         * Enrol user to (creators) institution. Every user have to be enrolled to an institution!
         */
        OrganizationRoleUser::firstOrCreate(
            [
                'user_id' => $user->id,
                'organization_id' => auth()->user()->current_organization_id,
            ],
            [
                'role_id' => 6, //student
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
        abort_unless(auth()->user()->mayAccessUser($user), 403);

        $roles = Role::all()->pluck('title', 'id');

        $user->load('roles');

        return view('users.edit', compact('roles', 'user'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        abort_unless(\Gate::allows('user_edit'), 403);
        abort_unless(auth()->user()->mayAccessUser($user), 403);

        $user->update($request->all());
        //$user->roles()->sync($request->input('roles', []));

        return redirect()->route('users.index');
    }

    public function massUpdate(MassUpdateUserRequest $request)
    {
        //check if currentUser can access requested user
        abort_unless((auth()->user()->role()->id == 1), 403);

        if (isset(request()->password[0])) {
            User::whereIn('id', request('ids'))->update([
                'password' => Hash::make($request->password),
            ]);
        }

        if (isset(request()->status_id[0])) {
            User::whereIn('id', request('ids'))->update([
                'status_id' => $request->status_id,
            ]);
        }

        return response(null, 204);
    }

    public function show(User $user)
    {
        abort_unless(\Gate::allows('user_show'), 403);
        abort_unless(((auth()->user()->role()->id == 1) or (auth()->user()->mayAccessUser($user))), 403);

        if (request()->wantsJson()) {
            return ['user' => $user];
        }

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
        abort_unless(((auth()->user()->role()->id == 1) or (auth()->user()->mayAccessUser($user))), 403);

        $return = $user->delete();
        //todo: concept to hard-delete users
        // - add user to db DeletedUser
        // - this DeletedUser.id gets data, which can't be deleted

        if (request()->wantsJson()) {
            return ['message' => $return];
        }
    }

    public function getCurrentUser()
    {
        if (request()->wantsJson()) {
            return ['user' => auth()->user()];
        }
    }

    /**
     * ! No soft delete !
     *
     * @param  MassDestroyUserRequest  $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function massDestroy(MassDestroyUserRequest $request)
    {
        abort_unless(\Gate::allows('user_delete'), 403);

        foreach (request('ids') as $id) {
            $this->forceDestroy(User::withTrashed()->find($id));
        }

        return response(null, 204);
    }

    public function setCurrentOrganization()
    {
        User::where('id', auth()->user()->id)->update([
            'current_period_id' => (request('current_period_id')) ? request('current_period_id') : 1,
            'current_organization_id' => request('current_organization_id'),
        ]);

        LogController::set('activeOrg', request('current_organization_id')); //set statistics

        return back();
    }

    public function setCurrentPeriod()
    {
        User::where('id', auth()->user()->id)->update([
            'current_period_id' => request('current_period_id'),
        ]);

        return back();
    }

    /**
     * Set users Profile Image
     *
     * @return type
     */
    public function setAvatar()
    {
        if (null !== request('medium_id')) {
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
        //check if currentUser can access requested user
        abort_unless(in_array(auth()->user()->current_organization_id, $user->organizations->pluck('id')->toArray()), 403);

        if (request()->wantsJson()) {
            return ['avatar' => ($user->medium_id !== null) ? '/media/'.$user->medium_id : (new \Laravolt\Avatar\Avatar)->create($user->fullName())->toBase64()];
        } else {
            return ($user->medium_id !== null) ? '/media/'.$user->medium_id : (new \Laravolt\Avatar\Avatar)->create($user->fullName())->toBase64();
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

    public function dsgvoExport($id)
    {
        abort_unless(auth()->user()->role()->id == 1, 403); //only admins!
        abort_unless(\Gate::allows('user_access'), 403);

        return User::where('id', auth()->user() - id())->with(['contactDetail', 'groups', 'roles', 'organizations', 'achievements'])->get()->first();
    }

    protected function validateImportRequest()
    {
        return request()->validate(
            [
                'medium_id' => 'required',
            ]
        );
    }

    public function forceDestroy(User $user, $permission = false)
    {
        if ($permission == false) {
            abort_unless(\Gate::allows('user_delete'), 403); //if permission != true (for API) check via Gate
            abort_unless(auth()->user()->role()->id == 1, 403); //only admins!
        }

        $fallback_user = User::firstOrCreate(
            ['common_name' => 'deleted_user'],
            [
                'username' => env('APP_FALLBACK_USER_USERNAME', 'Deleted User'),
                'firstname' => env('APP_FALLBACK_USER_FIRSTNAME', 'Deleted'),
                'lastname' => env('APP_FALLBACK_USER_LASTNAME', 'User'),
                'email' => env('APP_FALLBACK_USER_EMAIL', 'User'),
                'password' => Str::uuid(),
            ]
        );
        // absences
        $user->absences()->delete(); //user has absence  owner_id is an other user

        DB::table('absences')
            ->where('owner_id', $user->id)
            ->update(['owner_id' => $fallback_user->id]);

        //achievements
        $user->achievements()->delete();
        DB::table('achievements')
            ->where('owner_id', $user->id)
            ->update(['owner_id' => $fallback_user->id]);
        $user->artefacts()->delete();

        DB::table('certificates')
            ->where('owner_id', $user->id)
            ->update(['owner_id' => $fallback_user->id]);

        $user->contactDetail()->delete();

        // delete contents
        foreach ($user->contents as $content) {
            (new ContentController)->destroy($content, 'App\User', $user->id); // delete or unsubscribe if content is still subscribed elsewhere
        }
        DB::table('contents')
            ->where('owner_id', $user->id)
            ->update(['owner_id' => $fallback_user->id]);

        DB::table('content_subscriptions')
            ->where('owner_id', $user->id)
            ->update(['owner_id' => $fallback_user->id]);

        DB::table('curricula')
            ->where('owner_id', $user->id)
            ->update(['owner_id' => $fallback_user->id]);

        DB::table('enabling_objective_subscriptions')
            ->where('owner_id', $user->id)
            ->update(['owner_id' => $fallback_user->id]);

        DB::table('event_subscriptions')
            ->where('owner_id', $user->id)
            ->update(['owner_id' => $fallback_user->id]);

        $user->groups()->detach(); //expel

        DB::table('kanban_items')
            ->where('owner_id', $user->id)
            ->update(['owner_id' => $fallback_user->id]);
        DB::table('kanban_statuses')
            ->where('owner_id', $user->id)
            ->update(['owner_id' => $fallback_user->id]);
        DB::table('kanban_subscriptions')
            ->where('subscribable_type', "App\User")
            ->where('subscribable_id', $user->id)
            ->delete(); //delete individual subscriptions
        DB::table('kanban_subscriptions')
            ->where('owner_id', $user->id)
            ->update(['owner_id' => $fallback_user->id]);
        DB::table('kanban_item_subscriptions')
            ->where('subscribable_type', "App\User")
            ->where('subscribable_id', $user->id)
            ->delete(); //delete individual subscriptions
        DB::table('kanban_item_subscriptions')
            ->where('owner_id', $user->id)
            ->update(['owner_id' => $fallback_user->id]);
        DB::table('kanbans')
            ->where('owner_id', $user->id)
            ->update(['owner_id' => $fallback_user->id]);

        DB::table('logbook_entries')
            ->where('owner_id', $user->id)
            ->update(['owner_id' => $fallback_user->id]);
        DB::table('logbook_subscriptions')
            ->where('subscribable_type', "App\User")
            ->where('subscribable_id', $user->id)
            ->delete(); //delete individual subscriptions
        DB::table('logbook_subscriptions')
            ->where('owner_id', $user->id)
            ->update(['owner_id' => $fallback_user->id]);
        DB::table('logbooks')
            ->where('owner_id', $user->id)
            ->update(['owner_id' => $fallback_user->id]);

        //delete unused media
        foreach ($user->media() as $medium) {
            Medium::where('id', $medium->id)->delete();
        }
        DB::table('medium_subscriptions')
            ->where('owner_id', $user->id)
            ->update(['owner_id' => $fallback_user->id]);

        //Todo: harddelete messages Cmgmyr\Messenger

        $user->notifications()->delete();

        $user->organizations()->detach(); //expel

        $user->periods(); // inherit
        DB::table('periods')
            ->where('owner_id', $user->id)
            ->update(['owner_id' => $fallback_user->id]);

        DB::table('plan_subscriptions')
            ->where('subscribable_type', "App\User")
            ->where('subscribable_id', $user->id)
            ->delete(); //delete individual subscriptions
        DB::table('plan_subscriptions')
            ->where('owner_id', $user->id)
            ->update(['owner_id' => $fallback_user->id]);
        // todo: check if $user->ownsPlans() are subscribed, if not delete
        DB::table('plans')
            ->where('owner_id', $user->id)
            ->update(['owner_id' => $fallback_user->id]);

        $user->progresses()->delete();  //delete progresses

        DB::table('quote_subscriptions')
            ->where('owner_id', $user->id)
            ->update(['owner_id' => $fallback_user->id]);
        DB::table('quotes')
            ->where('owner_id', $user->id)
            ->update(['owner_id' => $fallback_user->id]);

        DB::table('reference_subscriptions')
            ->where('owner_id', $user->id)
            ->update(['owner_id' => $fallback_user->id]);
        DB::table('references')
            ->where('owner_id', $user->id)
            ->update(['owner_id' => $fallback_user->id]);

        DB::table('repository_subscriptions')
            ->where('owner_id', $user->id)
            ->update(['owner_id' => $fallback_user->id]);

        DB::table('task_subscriptions')
            ->where('owner_id', $user->id)
            ->update(['owner_id' => $fallback_user->id]);
        DB::table('tasks')
            ->where('owner_id', $user->id)
            ->update(['owner_id' => $fallback_user->id]);

        DB::table('terminal_objective_subscriptions')
            ->where('owner_id', $user->id)
            ->update(['owner_id' => $fallback_user->id]);

        $user->forceDelete();

        return redirect()->route('users.index');
    }
}

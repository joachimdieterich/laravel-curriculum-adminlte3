<?php

namespace App\Http\Controllers;

use App\Group;
use App\Http\Requests\MassDestroyUserRequest;
use App\Http\Requests\MassUpdateUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Imports\UsersImport;
use App\Medium;
use App\Organization;
use App\OrganizationRoleUser;
use App\Role;
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
        if (auth()->user()->role()->id > 6) {   //todo check: should students see all other user of current org?
            abort(403);
        }
        // every user should share with users of current organization except admins
        if (request()->wantsJson()) {
            $users = is_admin()
                ? User::noSharing()
                : Organization::find(auth()->user()->current_organization_id)->users();

            // only get users with teacher-role or higher
            if (request()->has('no_students') && !is_admin()) { // skip this step for admins
                $users = $users->whereNotIn('organization_role_users.role_id', [6, 7, 8, 9]); // student-, parent-, guest-, token-role
            }

            return getEntriesForSelect2ByCollection(
                $users,
                'users.',
                ['username', DB::raw("CONCAT(firstname, ' ', lastname)")],
                'lastname',
                "CONCAT(firstname, ' ', lastname)",
            );
        }
        else
        {
            return view('users.index');
        }
    }

    public function list()
    {
        $rowID = 'id';

        if (request()->has(['group_id']))
        {
            $request = request()->validate(
                [
                    'group_id' => 'required',
                ]
            );
            $users = Group::where('id',$request['group_id'])->first()->users();
            $rowID = 'user_id';
        }
        else
        {
            $users = (auth()->user()->role()->id == 1)
                ? User::select('id', 'username', 'firstname', 'lastname', 'common_name', 'email', 'medium_id', 'deleted_at')->noSharing()
                : Organization::find(auth()->user()->current_organization_id)->users()->noSharing();
        }

        return DataTables::of($users)
            ->setRowId($rowID)
            ->make(true);
    }

    public function create()
    {
        abort(405);
    }

    public function store(StoreUserRequest $request)
    {
        abort_unless(\Gate::allows('user_create'), 403);
        //todo: users should only be created in accessible organizations/roles
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

        if (request()->wantsJson()) {
            return $user;
        }
    }

    public function edit(User $user)
    {
        abort(405);
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        abort_unless(\Gate::allows('user_edit'), 403);
        abort_unless(auth()->user()->mayAccessUser($user), 403);

        $user->update($request->all());
        //$user->roles()->sync($request->input('roles', []));

        if (request()->wantsJson()) {
            return $user;
        }
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
        abort_unless(\Gate::allows('user_show'), 403, "Missing permission to view user");
        abort_unless(auth()->user()->mayAccessUser($user), 403, "No access to view user");

        if (request()->wantsJson()) {
            return ['user' => $user];
        }

        $status_definitions = StatusDefinition::all();
        $user->load('roles');
        $user->load(['organizations.state', 'organizations.country']);
        $user->load('groups');
        $user->load('contactDetail.owner');

        return view('users.show')
            ->with(compact('user'))
            ->with(compact('status_definitions'));
    }

    public function destroy(User $user)
    {
        abort_unless(\Gate::allows('user_delete'), 403);
        abort_unless(auth()->user()->mayAccessUser($user), 403);

        $return = $user->delete();
        //todo: concept to hard-delete users
        // - add user to db DeletedUser
        // - this DeletedUser.id gets data, which can't be deleted

        if (request()->wantsJson()) {
            return ['message' => $return];
        }
    }

   /* public function getCurrentUser()
    {
        if (request()->wantsJson()) {
            return ['user' => auth()->user()];
        }
    }*/

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
        abort_if(auth()->user()->id == 8 , 403); // only official guest user should not change current Organization

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

        return User::with(['contactDetail', 'groups', 'roles', 'organizations', 'achievements'])->find(auth()->user()->id);
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
            abort_unless(\Gate::allows('user_delete'), 403); //if permission != true (for API ) check via Gate
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
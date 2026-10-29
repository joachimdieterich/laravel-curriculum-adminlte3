<?php

namespace App\Http\Controllers;

use App\Organization;
use App\OrganizationRoleUser;
use App\StatusDefinition;
use App\User;
use Exception;
use Gate;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Throwable;
use Yajra\DataTables\DataTables;

class OrganizationsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse|View
     */
    public function index()
    {
        if (request()->wantsJson()) {
            if (is_admin()) {
                return getEntriesForSelect2ByModel(
                    "App\Organization"
                );
            }

            return getEntriesForSelect2ByCollection(
                auth()->user()->organizations(),
                'organizations.'
            );
        }

        if (! Gate::allows('organization_access')) {
            $validOrg = auth()->user()->organizationRolesUsers()->where('role_id', 4)->pluck('organization_id')->first();
            if ($validOrg == null) {
                abort(403);
            }

            try {
                auth()->user()->update(['current_organization_id' => $validOrg]);
            } catch (Throwable) {
                abort(403);
            }
        }

        return view('organizations.index');
    }

    /**
     * @throws Exception
     */
    public function list(): JsonResponse
    {
        abort_unless(Gate::allows('organization_access'), 403);

        return DataTables::of(Organization::viewable(is_admin(), auth()->user()->id))->make();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Organization|void
     */
    public function store()
    {
        abort_unless(Gate::allows('organization_create'), 403);

        $new_organization = $this->validateRequest();

        return Organization::firstOrCreate($new_organization);
    }

    /**
     * Display the specified resource.
     *
     * @return array|View
     */
    public function show(Organization $organization)
    {
        abort_unless(Gate::allows('organization_show'), 403);
        abort_unless(auth()->user()->organizations->contains($organization) || is_admin(), 403);

        $status_definitions = StatusDefinition::all();
        $organization       = $organization->load('type', 'state', 'country');

        return view('organizations.show')
            ->with(compact('organization'))
            ->with(compact('status_definitions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @return Organization|void
     */
    public function update(Organization $organization)
    {
        abort_unless(Gate::allows('organization_edit'), 403, 'No permission to edit organization information');
        abort_unless(auth()->user()->organizations->contains($organization) || is_admin(), 403, 'Need to be registered in this organization');

        $clean_data = $this->validateRequest();

        $organization->update($clean_data);

        return $organization->with(['country', 'state', 'type'])->find($organization->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return bool
     */
    public function destroy(Organization $organization)
    {
        abort_unless(Gate::allows('organization_delete'), 403);
        abort_unless(auth()->user()->organizations->contains($organization) || is_admin(), 403);

        return $organization->delete();
    }

    /**
     * @return array|void|null
     */
    public function enrol()
    {
        abort_unless(Gate::allows('organization_enrolment'), 403);

        $user = auth()->user();
        foreach ((request()->enrollment_list) as $enrolment) {
            $isAdmin = $user->id == $enrolment['user_id']
                 && $user->current_organization_id == $enrolment['organization_id']
                 && $user->role()->id == 1;

            // if admin -> do nothing -> admin should not degrade itself
            if (! $isAdmin && $user->role()->id <= $enrolment['role_id']) {
                // current admin should not be edited  // only allow roles below or equal
                abort_unless(($user->organizations->contains($enrolment['organization_id']) || is_admin()), 403);
                $return[] = OrganizationRoleUser::updateOrCreate(
                    [
                        'user_id'         => $enrolment['user_id'],
                        'organization_id' => $enrolment['organization_id'],
                    ],
                    [
                        'role_id' => $enrolment['role_id'],
                    ]
                );
            }
        }

        if (request()->wantsJson()) {
            return $return ?? null;
        }
    }

    /**
     * @return ?array|void
     */
    public function expel()
    {
        abort_unless(Gate::allows('organization_enrolment'), 403);
        $user    = auth()->user();
        $isAdmin = is_admin();

        foreach ((request()->expel_list) as $expel) {
            abort_unless(($user->organizations->contains($expel['organization_id']) || $isAdmin), 403);

            $return[] = OrganizationRoleUser::where([
                'user_id'         => $expel['user_id'],
                'organization_id' => $expel['organization_id'],
            ])->delete();

            // if users current_organization_id is equal to expelled organization reset current_organization_id
            $u = User::find($expel['user_id']);

            if ($u->current_organization_id == $expel['organization_id']) {
                $u->current_organization_id = $u->organizations()->first()->id;
                $u->save();
            }
        }

        if (request()->wantsJson()) {
            return $return ?? null;
        }
    }

    protected function validateRequest()
    {
        return request()->validate([
            'common_name'          => 'sometimes',
            'title'                => 'sometimes|required',
            'description'          => 'sometimes',
            'street'               => 'sometimes',
            'postcode'             => 'sometimes',
            'city'                 => 'sometimes',
            'state_id'             => 'sometimes',
            'country_id'           => 'sometimes',
            'organization_type_id' => 'sometimes',
            'phone'                => 'sometimes',
            'email'                => 'sometimes',
            'status_id'            => 'sometimes',
            'lms_url'              => 'sometimes',
        ]);
    }
}

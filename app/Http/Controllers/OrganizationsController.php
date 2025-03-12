<?php

namespace App\Http\Controllers;

use App\Organization;
use App\OrganizationRoleUser;
use App\StatusDefinition;
use App\User;
use Yajra\DataTables\DataTables;

class OrganizationsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->wantsJson()) {
            if (is_admin()) {
                return getEntriesForSelect2ByModel(
                    "App\Organization"
                );
            } else {
                return getEntriesForSelect2ByCollection(
                    auth()->user()->organizations(),
                    'organizations.'
                );
            }
        }
        abort_unless(\Gate::allows('organization_access'), 403);

        return view('organizations.index');
    }

    public function list()
    {
        abort_unless(\Gate::allows('organization_access'), 403);


        if (auth()->user()->role()->id == 1) {
            $organizations = Organization::with(['status']);
            $organization_id_field = 'id'; // if auth()->user()->organizations() is used query uses organization_role_user table therefore organization_id field = organization_id
        } else  {
            $organizations = auth()->user()->organizations()->with(['status']);
            $organization_id_field = 'organization_id';
        }

        return DataTables::of($organizations)
            ->addColumn('status', function ($organizations) {
                return $organizations->status->lang_de;
            })
            ->addColumn('check', '')
            ->setRowId($organization_id_field)
            ->setRowAttr([
                'color' => 'primary',
            ])
            ->make(true);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        abort_unless(\Gate::allows('organization_create'), 403);

        $new_organization = $this->validateRequest();
        $organization = Organization::firstOrCreate(
            array_merge($this->validateRequest(),
                [
                    'organization_type_id' => format_select_input($new_organization['organization_type_id']),
                    'state_id' => format_select_input($new_organization['state_id']),
                    'country_id' => format_select_input($new_organization['country_id']),
                ]
            )
        );

        if (request()->wantsJson()) {
            return $organization;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Organization $organization)
    {
        abort_unless(\Gate::allows('organization_show'), 403);
        abort_unless((auth()->user()->organizations->contains($organization) or is_admin()), 403);

        if (request()->wantsJson()) {
            return [
                'message' => $organization,
            ];
        }
        $status_definitions = StatusDefinition::all();
        $organization = $organization->load('type', 'state', 'country');
        return view('organizations.show')
                ->with(compact('organization'))
                ->with(compact('status_definitions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Organization $organization)
    {
        abort_unless(\Gate::allows('organization_edit'), 403);
        abort_unless((auth()->user()->organizations->contains($organization) or is_admin()), 403);

        $clean_data = $this->validateRequest();
        $clean_data['state_id'] = format_select_input($clean_data['state_id']);
        $clean_data['country_id'] = format_select_input($clean_data['country_id']);
        $clean_data['organization_type_id'] = format_select_input($clean_data['organization_type_id']);

        if (isset(request()->status_id[0])) {
            $clean_data['status_id'] = request()->status_id[0];  //hack to prevent array to string conversion
        }

        $organization->update($clean_data);

        if (request()->wantsJson()) {
            return $organization;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Organization $organization)
    {
        abort_unless(\Gate::allows('organization_delete'), 403);
        abort_unless((auth()->user()->organizations->contains($organization) or is_admin()), 403);

        return $organization->delete();
    }

    public function enrol()
    {
        abort_unless(\Gate::allows('organization_enrolment'), 403);

        foreach ((request()->enrollment_list) as $enrolment) {
            if (auth()->user()->id == $enrolment['user_id']
                and auth()->user()->role()->id == 1
                and auth()->user()->current_organization_id == $enrolment['organization_id']) {
                // do nothing -> admin should not degrade itself
            } else {
                //current admin should not be edited
                if (auth()->user()->role()->id <= $enrolment['role_id']) {  //only allow roles below or equal
                    abort_unless((auth()->user()->organizations->contains($enrolment['organization_id']) or is_admin()), 403);
                    $return[] = OrganizationRoleUser::updateOrCreate(
                        [
                            'user_id' => $enrolment['user_id'],
                            'organization_id' => $enrolment['organization_id'],
                        ],
                        [
                            'role_id' => $enrolment['role_id'],
                        ]
                    );
                }
            }
        }
        if (request()->wantsJson()) {
            return $return ?? null;
        }
    }

    public function expel()
    {
        abort_unless(\Gate::allows('organization_enrolment'), 403);

        foreach ((request()->expel_list) as $expel) {
            abort_unless((auth()->user()->organizations->contains($expel['organization_id']) or is_admin()), 403);
            $return[] = OrganizationRoleUser::where([
                'user_id' => $expel['user_id'],
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
            'common_name' => 'sometimes',
            'title' => 'sometimes|required',
            'description' => 'sometimes',
            'street' => 'sometimes',
            'postcode' => 'sometimes',
            'city' => 'sometimes',
            'state_id' => 'sometimes',
            'country_id' => 'sometimes',
            'organization_type_id' => 'sometimes',
            'phone' => 'sometimes',
            'email' => 'sometimes',
            'status_id' => 'sometimes',
            'lms_url' => 'sometimes',
        ]);
    }
}

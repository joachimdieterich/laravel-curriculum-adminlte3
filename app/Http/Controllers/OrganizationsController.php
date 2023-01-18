<?php

namespace App\Http\Controllers;

use App\Country;
use App\Http\Requests\MassDestroyOrganizationRequest;
use App\Organization;
use App\OrganizationRoleUser;
use App\OrganizationType;
use App\State;
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

        // select2 request
        if (request()->wantsJson() and request()->has(['term', 'page'])) {
            if (is_admin()) {
                abort_unless(\Gate::allows('organization_access'), 403);

                return  getEntriesForSelect2ByModel(
                    "App\Organization"
                );
            } else {
                return  getEntriesForSelect2ByCollection(
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
        $organizations = (auth()->user()->role()->id == 1) ? Organization::with(['status']) : auth()->user()->organizations()->with(['status']);

        $edit_gate = \Gate::allows('organization_edit');
        $delete_gate = \Gate::allows('organization_delete');

        return DataTables::of($organizations)
            ->addColumn('status', function ($organizations) {
                return $organizations->status->lang_de;
            })
            ->addColumn('action', function ($organizations) use ($edit_gate, $delete_gate) {
                $actions = '';
                if ($edit_gate) {
                    $actions .= '<a href="'.route('organizations.edit', $organizations->id).'"'
                                    .'id="edit-organization-'.$organizations->id.'" '
                                    .'class="btn p-1">'
                                    .'<i class="fa fa-pencil-alt"></i>'
                                    .'</a>';
                }
                if ($delete_gate) {
                    $actions .= '<button type="button" '
                                .'class="btn text-danger" '
                                .'onclick="destroyDataTableEntry(\'organizations\','.$organizations->id.')">'
                                .'<i class="fa fa-trash"></i></button>';
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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_unless(\Gate::allows('organization_create'), 403);
        $status_definitions = StatusDefinition::all();
        $organization_types = OrganizationType::all();
        $countries = Country::all();
        $states = State::where('country', 'DE')->get();

        return view('organizations.create')
            ->with(compact('status_definitions'))
            ->with(compact('countries'))
            ->with(compact('states'))
            ->with(compact('organization_types'));
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

        // axios call?
        if (request()->wantsJson()) {
            return ['message' => $organization->path()];
        }
        //dd($organization->path());
        return redirect($organization->path());
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

        return view('organizations.show')
                ->with(compact('organization'))
                ->with(compact('status_definitions'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Organization $organization)
    {
        abort_unless(\Gate::allows('organization_edit'), 403);
        abort_unless((auth()->user()->organizations->contains($organization) or is_admin()), 403);
        $status_definitions = StatusDefinition::all();
        $organization_types = OrganizationType::all();
        $countries = Country::all();
        $states = State::where('country', 'DE')->get();

        return view('organizations.edit')
            ->with(compact('organization'))
            ->with(compact('countries'))
            ->with(compact('states'))
            ->with(compact('status_definitions'))
            ->with(compact('organization_types'));
    }

    public function editAddress(Organization $organization)
    {
        abort_unless(\Gate::allows('organization_edit_address'), 403);
        abort_unless((auth()->user()->organizations->contains($organization) or is_admin()), 403);

        return view('organizations.editAddress')
            ->with(compact('organization'));
    }

    public function editLmsUrl(Organization $organization)
    {
        abort_unless(\Gate::allows('organization_edit_address'), 403);
        abort_unless((auth()->user()->organizations->contains($organization) or is_admin()), 403);

        return view('organizations.editLmsUrl')
            ->with(compact('organization'));
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

        return redirect($organization->path());
    }

    public function updateAddress(Organization $organization)
    {
        abort_unless(\Gate::allows('organization_edit_address'), 403);
        abort_unless((auth()->user()->organizations->contains($organization) or is_admin()), 403);

        $clean_data = $this->validateRequest();
        $organization->update($clean_data);

        return redirect($organization->path());
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

        $organization->delete();

        return back();
    }

    public function massDestroy(MassDestroyOrganizationRequest $request)
    {
        abort_unless(\Gate::allows('organization_delete'), 403);
        Organization::whereIn('id', request('ids'))->delete();

        return response(null, 204);
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

        return $return;
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

        return $return;
    }

    protected function validateRequest()
    {
        return request()->validate([
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
            'lms_url' => 'sometimes|url',
        ]);
    }
}

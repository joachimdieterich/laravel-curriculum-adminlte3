<?php

namespace App\Http\Controllers;

use App\Country;
use App\OrganizationType;
use App\State;
use Yajra\DataTables\DataTables;

class OrganizationTypesController extends Controller
{
    public function index()
    {
        abort_unless(\Gate::allows('organization_type_access'), 403);

        $organization_types = OrganizationType::all();

        return view('organizationtypes.index', compact('organization_types'));
    }

    public function list()
    {
        abort_unless(\Gate::allows('organization_type_access'), 403);

        $organization_types = OrganizationType::select([
            'id',
            'title',
            'external_id',
            'state_id',
            'country_id', ]);

        $edit_gate = \Gate::allows('organization_type_edit');
        $delete_gate = \Gate::allows('organization_type_delete');

        return DataTables::of($organization_types)
            ->addColumn('action', function ($organization_types) use ($edit_gate, $delete_gate) {
                $actions = '';
                if ($edit_gate) {
                    $actions .= '<a href="'.route('organizationtypes.edit', $organization_types->id).'" '
                                    .'id="edit-organization-type-'.$organization_types->id.'" '
                                    .'class="btn">'
                                    .'<i class="fa fa-pencil-alt"></i>'
                                    .'</a>';
                }
                if ($delete_gate) {
                    $actions .= '<button type="button" '
                                .'class="btn text-danger" '
                                .'onclick="destroyDataTableEntry(\'organizationtypes\','.$organization_types->id.')">'
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

    public function create()
    {
        abort_unless(\Gate::allows('organization_type_create'), 403);
        $countries = Country::all();

        $states = State::all()->sortBy('country');

        return view('organizationtypes.create')
                    ->with(compact('countries'))
                    ->with(compact('states'));
    }

    public function store()
    {
        abort_unless(\Gate::allows('organization_type_create'), 403);

        $input = $this->validateRequest();
        $input_state = State::where('code', format_select_input($input['state_id']))->get()->first();

        $organization_types = OrganizationType::create([
            'title'         => $input['title'],
            'external_id'   => $input['external_id'],
            'state_id'      => $input_state->code,
            'country_id'    =>  $input_state->country,
        ]);

        return redirect()->route('organizationtypes.index');
    }

    public function edit(OrganizationType $organizationtype)
    {
        abort_unless(\Gate::allows('organization_type_edit'), 403);
        $countries = Country::all();
        $states = State::all();

        return view('organizationtypes.edit')
                    ->with(compact('organizationtype'))
                    ->with(compact('countries'))
                    ->with(compact('states'));
    }

    public function update(OrganizationType $organizationtype)
    {
        abort_unless(\Gate::allows('organization_type_edit'), 403);
        $input = $this->validateRequest();
        $input_state = State::where('code', format_select_input($input['state_id']))->get()->first();
        $organizationtype->update([
            'title' => $input['title'],
            'external_id' => $input['external_id'],
            'state_id' => $input_state->code,
            'country_id' => $input_state->country,
        ]);

        return redirect()->route('organizationtypes.index');
    }

    public function show(OrganizationType $organizationtype)
    {
        abort_unless(\Gate::allows('organization_type_show'), 403);
        $countries = Country::all();
        $states = State::all();

        return view('organizationtypes.show')
                    ->with(compact('organizationtype'))
                    ->with(compact('countries'))
                    ->with(compact('states'));
    }

    public function destroy(OrganizationType $organizationtype)
    {
        abort_unless(\Gate::allows('organization_type_delete'), 403);

        $organizationtype->delete();

        return back();
    }

    protected function validateRequest()
    {
        return request()->validate([
            'title'         => 'sometimes|required',
            'external_id'   => 'sometimes',
            'state_id'      => 'sometimes',
            'country_id'    => 'sometimes',
            'created_at'    => 'sometimes',
            'updated_at'    => 'sometimes',
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\OrganizationType;
use App\State;
use Yajra\DataTables\DataTables;

class OrganizationTypesController extends Controller
{
    public function index()
    {

        if (request()->wantsJson()) {

            return getEntriesForSelect2ByModel(
                "App\OrganizationType"
            );
        }
        else
        {
            abort_unless(\Gate::allows('organization_type_access'), 403);

            $organization_types = OrganizationType::all();

            return view('organizationtypes.index', compact('organization_types'));
        }

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

        return DataTables::of($organization_types)
            ->addColumn('check', '')
            ->setRowId('id')
            ->setRowAttr([
                'color' => 'primary',
            ])
            ->make(true);
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

        if (request()->wantsJson()) {
            return $organization_types;
        }
    }

    public function update(OrganizationType $organizationType)
    {
        abort_unless(\Gate::allows('organization_type_edit'), 403);
        $input = $this->validateRequest();
        $input_state = State::where('code', format_select_input($input['state_id']))->get()->first();
        $organizationType->update([
            'title' => $input['title'],
            'external_id' => $input['external_id'],
            'state_id' => $input_state->code,
            'country_id' => $input_state->country,
        ]);

        if (request()->wantsJson()) {
            return $organizationType;
        }
    }

    public function show(OrganizationType $organizationType)
    {

        abort_unless(\Gate::allows('organization_type_show'), 403);

        $organizationType = $organizationType->load( 'country', 'state');

        return view('organizationtypes.show')
                    ->with(compact('organizationType'));
    }

    public function destroy(OrganizationType $organizationType)
    {
        abort_unless(\Gate::allows('organization_type_delete'), 403);

        return $organizationType->delete();
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

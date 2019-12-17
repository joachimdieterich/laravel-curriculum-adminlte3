<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyProductRequest;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\OrganizationType;
use App\Country;
use App\State;
use Illuminate\Http\Request;
use Redirect,Response,DB,Config;
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
            'country_id']);
        
        return DataTables::of($organization_types)
            ->addColumn('action', function ($organization_types) {
                 $actions  = '';
                    if (\Gate::allows('organization_type_show')){
                        $actions .= '<a href="'.route('organizationtypes.show', $organization_types->id).'" '
                                    . 'id="show-organization-type-'.$organization_types->id.'" '
                                    . 'class="btn btn-xs btn-success mr-1">'
                                    . '<i class="fa fa-list-alt"></i>'
                                    . '</a>';
                    }
                    if (\Gate::allows('organization_type_edit')){
                        $actions .= '<a href="'.route('organizationtypes.edit', $organization_types->id).'" '
                                    . 'id="edit-organization-type-'.$organization_types->id.'" '
                                    . 'class="btn btn-xs btn-primary text-white  mr-1">'
                                    . '<i class="fa fa-edit"></i>' 
                                    . '</a>';
                    }
                    if (\Gate::allows('organization_type_delete')){
                        $actions .= '<form action="'.route('organizationtypes.destroy', $organization_types->id).'" method="POST" class="pull-right">'
                                    . '<input type="hidden" name="_method" value="delete">'. csrf_field().''
                                    . '<button type="submit" '
                                    . 'id="delete-organization-type-'.$organization_types->id.'" '
                                    . 'class="btn btn-xs btn-danger"><i class="fa fa-trash"></i></button>';
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
       
        $organizationtype->update($this->validateRequest());

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

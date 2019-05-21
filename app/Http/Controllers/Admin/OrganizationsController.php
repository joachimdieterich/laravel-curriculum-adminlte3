<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Organization;
use App\Http\Requests\MassDestroyOrganizationRequest;
use Illuminate\Http\Request;
use App\Role;
use App\Status;
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
        $organizations = Organization::all();//auth()->user()->accessibleOrganizations(); //Organization::all();
        //$statuses = Status::all();
        //dd($statuses);
        return view('admin.organizations.index')
                ->with(compact('organizations'));
        
        
    }

    public function organizationList()
    {
        $organizations = Organization::select([
            'id', 
            'title', 
            'description', 
            'street', 
            'postcode',
            'city',
            'phone',
            'email', 
            'status_id'
            ]);
        
        return DataTables::of($organizations)
            ->addColumn('status', function ($organizations) {
                return $organizations->status()->first()->lang_de;                
            })
            ->addColumn('action', function ($organizations) {
                 $actions  = '';
                    if (\Gate::allows('organization_show')){
                        $actions .= '<a href="'.route('admin.organizations.show', $organizations->id).'" '
                                    . 'class="btn btn-xs btn-success">'
                                    . '<i class="fa fa-list-alt"></i> Show'
                                    . '</a>';
                    }
                    if (\Gate::allows('organization_edit')){
                        $actions .= '<a href="'.route('admin.organizations.edit', $organizations->id).'" '
                                    . 'class="btn btn-xs btn-primary">'
                                    . '<i class="fa fa-edit"></i> Edit'
                                    . '</a>';
                    }
                    if (\Gate::allows('organization_delete')){
                        $actions .= '<form action="'.route('admin.organizations.destroy', $organizations->id).'" method="POST">'
                                    . '<input type="hidden" name="_method" value="delete">'. csrf_field().''
                                    . '<button type="submit" class="btn btn-xs btn-danger"><i class="fa fa-trash"></i> Delete</button>';
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
        return view('admin.organizations.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        // validate
        
        //persist
        //dd(auth()->user()->organizations());

        $organization = Organization::firstOrCreate($this->validateRequest());
        
        // axios call? 
        if (request()->wantsJson()){    
            return ['message' => $organization->path()];
        }
        
        return redirect($organization->path());
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Organization $organization)
    {
        $statuses = Status::all();
        
        return view('admin.organizations.show')
                ->with(compact('organization'))
                ->with(compact('statuses'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Organization $organization)
    {
        $statuses = Status::all();
        return view('admin.organizations.edit')
                ->with(compact('organization'))
                ->with(compact('statuses'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Organization $organization)
    {
        //$this->authorize('update', $organization);
        $clean_data = $this->validateRequest();
        if (isset(request()->status_id[0]))
        {
            $clean_data['status_id'] =  request()->status_id[0];  //hack to prevent array to string conversion
        }
        
        $organization->update($clean_data);
        
        return redirect($organization->path());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Organization $organization)
    {
        abort_unless(\Gate::allows('organization_delete'), 403);

        $organization->delete();

        return back();
    }
    
    public function massDestroy(MassDestroyOrganizationRequest $request)
    {
        Organization::whereIn('id', request('ids'))->delete();

        return response(null, 204);
    }
    
    
    protected function validateRequest()
    {               
        
        return request()->validate([
            'title'         => 'sometimes|required',
            'description'   => 'sometimes',
            'street'        => 'sometimes',
            'postcode'      => 'sometimes',
            'city'          => 'sometimes',
            'state_id'      => 'sometimes',
            'country_id'    => 'sometimes',
            'organization_type_id' => 'sometimes',
            'phone'         => 'sometimes',
            'email'         => 'sometimes',
            'status_id'     => 'sometimes',
            ]);
    }
}

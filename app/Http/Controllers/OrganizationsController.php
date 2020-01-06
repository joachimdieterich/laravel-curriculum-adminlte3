<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Organization;
use App\Http\Requests\MassDestroyOrganizationRequest;
use App\Status;
use App\OrganizationRoleUser;
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
        abort_unless(\Gate::allows('organization_access'), 403);
        $organizations = Organization::all();//auth()->user()->accessibleOrganizations(); 
        
        return view('organizations.index')
                ->with(compact('organizations'));
      
    }

    public function list()
    {
        abort_unless(\Gate::allows('organization_access'), 403);
        $organizations = Organization::select([
            'id', 
            'title', 
            'description', 
            'street', 
            'postcode',
            'city',
            'status_id'
            ]);
        
        return DataTables::of($organizations)
            ->addColumn('status', function ($organizations) {
                return $organizations->status()->first()->lang_de;                
            })
            ->addColumn('action', function ($organizations) {
                 $actions  = '';
                    if (\Gate::allows('organization_show')){
                        $actions .= '<a href="'.route('organizations.show', $organizations->id).'" '
                                    . 'id="show-organization-'.$organizations->id.'" '
                                    . 'class="btn btn-xs btn-success mr-1">'
                                    . '<i class="fa fa-list-alt"></i>'
                                    . '</a>';
                    }
                    if (\Gate::allows('organization_edit')){
                        $actions .= '<button onclick="app.__vue__.$modal.show(\'organization-modal\', {\'id\':\''.$organizations->id.'\', \'method\': \'patch\'})"'
                                    . 'id="edit-organization-'.$organizations->id.'" '
                                    . 'class="btn btn-xs btn-primary text-white  mr-1">'
                                    . '<i class="fa fa-edit"></i>' 
                                    . '</button>';
                    }
                    if (\Gate::allows('organization_delete')){
                        $actions .= '<form action="'.route('organizations.destroy', $organizations->id).'" method="POST" class="pull-right">'
                                    . '<input type="hidden" name="_method" value="delete">'. csrf_field().''
                                    . '<button type="submit" '
                                    . 'id="delete-organization-'.$organizations->id.'" '
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
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_unless(\Gate::allows('organization_create'), 403);
        $statuses = Status::all();
        
        return view('organizations.create')
                ->with(compact('statuses'));
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
        abort_unless(\Gate::allows('organization_create'), 403);
        $organization = Organization::firstOrCreate($this->validateRequest());
        
        // axios call? 
        if (request()->wantsJson()){    
            return ['message' => $organization->path()];
        }
        //dd($organization->path());
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
        abort_unless(\Gate::allows('organization_show'), 403);
        // axios call? 
        if (request()->wantsJson()){   
            return [
                'message' => $organization
            ];
        }
        $statuses = Status::all();
        return view('organizations.show')
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
        abort_unless(\Gate::allows('organization_edit'), 403);
        $statuses = Status::all();
        return view('organizations.edit')
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
        abort_unless(\Gate::allows('organization_edit'), 403);
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
        abort_unless(\Gate::allows('organization_delete'), 403);
        Organization::whereIn('id', request('ids'))->delete();

        return response(null, 204);
    }
    
    public function enrol()
    {
        abort_unless(\Gate::allows('organization_enrolment'), 403);
        
        foreach ((request()->enrollment_list) AS $enrolment)
        {  
            
            $return[] = OrganizationRoleUser::firstOrCreate(
                    [
                        'user_id'         => $enrolment['user_id'],
                        'organization_id' => $enrolment['organization_id']
                    ],
                    [
                        'role_id'         => $enrolment['role_id']
                    ]
                );
        }
        
        return $return;  
    }
    
    public function expel()
    {
        abort_unless(\Gate::allows('organization_enrolment'), 403);
        
        foreach ((request()->expel_list) AS $expel)
        {  
            $return[] = OrganizationRoleUser::where([
                                        'user_id'         => $expel['user_id'],
                                        'organization_id' => $expel['organization_id'],
                                    ])->delete();
        }
        
        return $return;  
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

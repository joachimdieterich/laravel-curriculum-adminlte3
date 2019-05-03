<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Organization;
use App\Http\Requests\MassDestroyOrganizationRequest;
use Illuminate\Http\Request;
use App\Role;

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
        return view('admin.organizations.index', compact('organizations'));
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
        return view('admin.organizations.show', compact('organization'));
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
        return view('admin.organizations.edit', compact('organization'));
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
        $organization->update($this->validateRequest());
        
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
        /* todo set status by admin? */
        request()->merge([      
            'status' => 1, 
            
        ]);
        
        return request()->validate([
            'title'         => 'sometimes|required',
            'description'   => 'sometimes',
            'street'        => 'sometimes',
            'postcode'      => 'sometimes',
            'city'          => 'sometimes',
            'phone'         => 'sometimes',
            'email'         => 'sometimes',
            'status'        => 'required',
            ]);
    }
}

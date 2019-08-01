<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyProductRequest;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\OrganizationType;
use Illuminate\Http\Request;
use Redirect,Response,DB,Config;
use Yajra\DataTables\DataTables;

class OrganizationTypesController extends Controller
{
    public function index()
    {
      
        abort_unless(\Gate::allows('organization_type_access'), 403);

        $organization_types = OrganizationType::all();
        

        return view('admin.organizationtypes.index', compact('organization_types'));
    }
    
    public function organizationTypeList()
    {
        abort_unless(\Gate::allows('organization_type_access'), 403);
  
        $organization_types = OrganizationType::select(['id', 'title', 'external_id', 'state_id', 'country_id']);
        
        return DataTables::of($organization_types)
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

        return view('admin.organizationtype.create');
    }

    public function store(StoreOrganizationTypeRequest $request)
    {
        abort_unless(\Gate::allows('organization_type_create'), 403);

        $organization_types = OrganizationType::create($request->all());

        return redirect()->route('admin.organizationtype.index');
    }

    public function edit(OrganizationType $organization_type)
    {
        abort_unless(\Gate::allows('organization_type_edit'), 403);

        return view('admin.organizationtype.edit', compact('organization_type'));
    }

    public function update(UpdateOrganizationTypeRequest $request, OrganizationType $organization_type)
    {
        abort_unless(\Gate::allows('organization_type_edit'), 403);

        $organization_type->update($request->all());

        return redirect()->route('admin.organizationtype.index');
    }

    public function show(OrganizationType $organization_type)
    {
        abort_unless(\Gate::allows('organization_type_show'), 403);

        return view('admin.organizationtype.show', compact('organization_type'));
    }

    public function destroy(OrganizationType $organization_type)
    {
        abort_unless(\Gate::allows('organization_type_delete'), 403);

        $organization_type->delete();

        return back();
    }

    public function massDestroy(MassDestroyOrganizationTypeRequest $request)
    {
        OrganizationType::whereIn('id', request('ids'))->delete();

        return response(null, 204);
    }
}

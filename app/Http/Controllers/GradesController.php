<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyProductRequest;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Grade;
use Yajra\DataTables\DataTables;

class GradesController extends Controller
{
    public function index()
    {
        abort_unless(\Gate::allows('grade_access'), 403);

        return view('grades.index');
    }
    
    public function list()
    {
        abort_unless(\Gate::allows('grade_access'), 403);
        $grades = Grade::select([
            'id', 
            'title', 
            'external_begin', 
            'external_end', 
            'organization_type_id',
            ]);       
        
        return DataTables::of($grades)
            ->addColumn('organization_type', function ($grades) {
                return isset($grades->organizationType()->first()->title) ? $grades->organizationType()->first()->title : 'default';                
            })
           
            ->addColumn('action', function ($grades) {
                 $actions  = '';
                    if (\Gate::allows('grade_show')){
                        $actions .= '<a href="'.route('grades.show', $grades->id).'" '
                                    . 'id="show-grade-'.$grades->id.'" '
                                    . 'class="btn btn-xs btn-success mr-1">'
                                    . '<i class="fa fa-list-alt"></i>'
                                    . '</a>';
                    }
                    if (\Gate::allows('grade_edit')){
                        $actions .= '<a href="'.route('grades.edit', $grades->id).'" '
                                    . 'id="edit-grade-'.$grades->id.'" '
                                    . 'class="btn btn-xs btn-primary mr-1">'
                                    . '<i class="fa fa-edit"></i>'
                                    . '</a>';
                    }
                    if (\Gate::allows('grade_delete')){
                        $actions .= '<form action="'.route('grades.destroy', $grades->id).'" method="POST" class="pull-right">'
                                    . '<input type="hidden" name="_method" value="delete">'. csrf_field().''
                                    . '<button '
                                    . 'type="submit" '
                                    . 'id="delete-grade-'.$grades->id.'" '
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
        abort_unless(\Gate::allows('grade_create'), 403);
        $organization_types = \App\OrganizationType::all();
        return view('grades.create')
                ->with(compact('organization_types'));
    }

    public function store()
    {
        abort_unless(\Gate::allows('grade_create'), 403);
        $new_grade = $this->validateRequest();
        
        $grades = Grade::create([
            'title' => $new_grade['title'],
            'external_begin' => $new_grade['external_begin'],
            'external_end' => $new_grade['external_end'],
            'organization_type_id' => format_select_input($new_grade['organization_type_id'])
        ]);

        return redirect()->route('grades.index');
    }

    public function edit(Grade $grade)
    {
        abort_unless(\Gate::allows('grade_edit'), 403);

        $organization_types = \App\OrganizationType::all();
        return view('grades.edit')
                ->with(compact('grade'))
                ->with(compact('organization_types'));
    }

    public function update(Grade $grade)
    {
        abort_unless(\Gate::allows('grade_edit'), 403);

        $new_grade = $this->validateRequest();
        $grade->update([
            'title' => $new_grade['title'],
            'external_begin' => $new_grade['external_begin'],
            'external_end' => $new_grade['external_end'],
            'organization_type_id' => format_select_input($new_grade['organization_type_id'])
        ]);

        return redirect()->route('grades.index');
    }

    public function show(Grade $grade)
    {
        abort_unless(\Gate::allows('grade_show'), 403);

        return view('grades.show', compact('grade'));
    }

    public function destroy(Grade $grade)
    {
        abort_unless(\Gate::allows('grade_delete'), 403);

        $grade->delete();

        return back();
    }

    public function massDestroy()
    {
        Grade::whereIn('id', request('ids'))->delete();

        return response(null, 204);
    }
    
    protected function validateRequest()
    {   
        return request()->validate([
            'title'                  => 'sometimes|required',
            'external_begin'         => 'sometimes|required',
            'external_end'           => 'sometimes|required',
            'organization_type_id'   => 'sometimes|required',
            
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Curriculum;
use App\Grade;
use App\Subject;
use App\OrganizationType;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class CurriculumController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_unless(\Gate::allows('curriculum_access'), 403);

        $curricula = Curriculum::all();
       
        //dd($curricula);
        return view('curricula.index')
          ->with(compact('curricula'));
    }
    
     public function curriculaList()
    {
        $curricula = Curriculum::select([
            'id', 
            'title', 
            'state_id',
            'country_id',
            'grade_id',
            'subject_id',
            'owner_id',
            
            ]);
        //dd($curricula);
        return DataTables::of($curricula)
            ->addColumn('state', function ($curricula) {
                return $curricula->state()->first()->lang_de;                
            })
            ->addColumn('country', function ($curricula) {
                return $curricula->country()->first()->lang_de;                
            })
            ->addColumn('grade', function ($curricula) {
                return $curricula->grade()->first()->title;                
            })
            ->addColumn('subject', function ($curricula) {
                return $curricula->subject()->first()->title;                
            })
            ->addColumn('owner', function ($curricula) {
                return $curricula->owner()->first()->firstname.' '.$curricula->owner()->first()->lastname;                
            })
            ->addColumn('action', function ($curricula) {
                 $actions  = '';
                    if (\Gate::allows('curriculum_show')){
                        $actions .= '<a href="'.route('curricula.show', $curricula->id).'" '
                                    . 'class="btn btn-xs btn-success">'
                                    . '<i class="fa fa-list-alt"></i> Show'
                                    . '</a>';
                    }
                    if (\Gate::allows('curriculum_edit')){
                        $actions .= '<a href="'.route('curricula.edit', $curricula->id).'" '
                                    . 'class="btn btn-xs btn-primary">'
                                    . '<i class="fa fa-edit"></i> Edit'
                                    . '</a>';
                    }
                    if (\Gate::allows('curriculum_delete')){
                        $actions .= '<button type="button" class="btn btn-xs btn-danger" onclick="destroyCurriculum('.$curricula->id.')"><i class="fa fa-trash"></i> Delete</button>';
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
        $grades = Grade::all();
        $subjects   = Subject::all();
        $organization_types = OrganizationType::all();
        
        return view('curricula.create')
                ->with(compact('grades'))
                ->with(compact('subjects'))
                ->with(compact('organization_types'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $this->validateRequest();
        //dd($input);
        $curriculum = Curriculum::firstOrCreate([
            'title'                 => $input['title'],
            'description'           => $input['description'],
            'author'                => $input['author'],
            'publisher'             => $input['publisher'],
            'city'                  => $input['city'],
            'date'                  => $input['date'],
            'color'                 => $input['color'],
            'grade_id'              => $input['grade_id'][0],
            'subject_id'            => $input['subject_id'][0],
            'organization_type_id'  => $input['organization_type_id'][0],
            'state_id'              => 'DE-RP',
            'country_id'            => 'DE',
            'file_id'               => null,
            'owner_id'              => auth()->user()->id,
            
        ]);
        
        // axios call? 
        if (request()->wantsJson()){    
            return ['message' => $curriculum->path()];
        }
        
        return redirect($curriculum->path());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Curriculum  $curriculum
     * @return \Illuminate\Http\Response
     */
    public function show(Curriculum $curriculum)
    {
        
        return view('curricula.show')
                ->with(compact('curriculum'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Curriculum  $curriculum
     * @return \Illuminate\Http\Response
     */
    public function edit(Curriculum $curriculum)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Curriculum  $curriculum
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Curriculum $curriculum)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Curriculum  $curriculum
     * @return \Illuminate\Http\Response
     */
    public function destroy(Curriculum $curriculum)
    {
        //
    }
    
    protected function validateRequest()
    {               
        
        return request()->validate([
            'title'                 => 'sometimes|required',
            'description'           => 'sometimes',
            'author'                => 'sometimes',
            'publisher'             => 'sometimes',
            'city'                  => 'sometimes',
            'date'                  => 'sometimes',
            'color'                 => 'sometimes',
            'grade_id'              => 'sometimes',
            'subject_id'            => 'sometimes',
            'organization_type_id'  => 'sometimes',
            'state_id'              => 'sometimes',
            'country_id'            => 'sometimes',
            'file_id'               => 'sometimes',
            'owner_id'              => 'sometimes',
            ]);
    }
}

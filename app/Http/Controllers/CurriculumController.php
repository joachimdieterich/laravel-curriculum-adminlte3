<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Curriculum;
use App\Grade;
use App\Subject;
use App\OrganizationType;
use App\TerminalObjective;
use App\EnablingObjective;
use App\Medium;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use DOMDocument;
use App\Content;
use App\Glossar;
use App\Group;
use App\Country;
use App\State;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Illuminate\Support\Facades\DB;

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
       
        if (request()->wantsJson()){    
            return ['curricula' => $curricula];
        }
        return view('curricula.index')
          ->with(compact('curricula'));
    }
    
    public function list()
    {
        abort_unless(\Gate::allows('curriculum_access'), 403);
        
        
        $curricula = Curriculum::select([
            'id', 
            'title', 
            'state_id',
            'country_id',
            'grade_id',
            'subject_id',
            'owner_id',
            
            ]);
        
        return DataTables::of($curricula)
            ->addColumn('state', function ($curricula) {
                return isset($curricula->state()->first()->lang_de) ? $curricula->state()->first()->lang_de : '-';                
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
                                    . 'class="btn btn-xs btn-success mr-1">'
                                    . '<i class="fa fa-list-alt"></i>'
                                    . '</a>';
                    }
                    if (\Gate::allows('curriculum_edit') AND ($curricula->owner_id == auth()->user()->id)){
                        $actions .= '<a href="'.route('curricula.edit', $curricula->id).'" '
                                    . 'class="btn btn-xs btn-primary mr-1">'
                                    . '<i class="fa fa-edit"></i>'
                                    . '</a>';
                    }
                    if (\Gate::allows('curriculum_delete') AND ($curricula->owner_id == auth()->user()->id)){
                        $actions .= '<button type="button" class="btn btn-xs btn-danger" onclick="destroyCurriculum('.$curricula->id.')"><i class="fa fa-trash"></i></button>';
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
        abort_unless(\Gate::allows('curriculum_create'), 403);
        
        $grades = Grade::all();
        $subjects   = Subject::all();
        $organization_types = OrganizationType::all();
        
        $countries = Country::all();
        $states = State::where('country', 'DE')->get();
        
        return view('curricula.create')
                ->with(compact('grades'))
                ->with(compact('subjects'))
                ->with(compact('countries'))
                ->with(compact('states'))
                ->with(compact('organization_types'))
                ;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        abort_unless(\Gate::allows('curriculum_create'), 403);
        $input = $this->validateRequest();
         
        $curriculum = Curriculum::firstOrCreate([
            'title'                 => $input['title'],
            'description'           => $input['description'],
            'author'                => $input['author'],
            'publisher'             => $input['publisher'],
            'city'                  => $input['city'],
            'date'                  => $input['date'],
            'color'                 => $input['color'],
            'grade_id'              => format_select_input($input['grade_id']),
            'subject_id'            => format_select_input($input['subject_id']),
            'organization_type_id'  => format_select_input($input['organization_type_id']),
            'state_id'              => format_select_input($input['state_id']),
            'country_id'            => format_select_input($input['country_id']),
            'medium_id'             => $this->getMediumIdByInputFilepath($input),
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
    public function show(Curriculum $curriculum, $achievements = false)
    {
        
        abort_unless(\Gate::allows('curriculum_show'), 403);
        //check if user is enrolled or admin -> else 403 
        
        abort_unless((auth()->user()->curricula()->contains('id', $curriculum->id) // user enrolled
                  OR (auth()->user()->currentRole()->first()->id == 1)), 403);     // or admin

        $objectiveTypes = \App\ObjectiveType::all();
        $levels = \App\Level::all();
        
        $curriculum = Curriculum::with(['terminalObjectives', 
                        //'terminalObjectives.media', 
                        'terminalObjectives.mediaSubscriptions', 
                        'terminalObjectives.referenceSubscriptions.siblings.referenceable', 
                        'terminalObjectives.quoteSubscriptions.siblings.quotable', 
                        'terminalObjectives.achievements' => function($query) {
                            $query->where('user_id', auth()->user()->id);
                        },
                        'terminalObjectives.enablingObjectives', 
                        //'terminalObjectives.enablingObjectives.media',
                        'terminalObjectives.enablingObjectives.mediaSubscriptions', 
                        'terminalObjectives.enablingObjectives.referenceSubscriptions.siblings.referenceable', 
                        'terminalObjectives.enablingObjectives.quoteSubscriptions.siblings.quotable', 
                        'terminalObjectives.enablingObjectives.achievements' => function($query) {
                            $query->where('user_id', auth()->user()->id);
                        },        
                        'contentSubscriptions.content', 
                        'glossar.contents', 
                        'media'])
                        ->find($curriculum->id);
        $settings= json_encode([
            'edit' => true,
            'cross_reference_curriculum_id' => false
        ]);
        
        return view('curricula.show')
                ->with(compact('curriculum'))
                ->with(compact('objectiveTypes'))
                ->with(compact('levels'))
                ->with(compact('settings'));
    }
    /**
     * Display the specified resource with achievements.
     *
     * @param  \App\Curriculum  $curriculum
     * @return \Illuminate\Http\Response
     */
    public function showAchievements(Curriculum $curriculum)
    {
        $this->show($curriculum, true);
    }
    
    public function getObjectives(Curriculum $curriculum)
    {
        $curriculum = Curriculum::where('id', $curriculum->id)->with('terminalObjectives.enablingObjectives')->get()->first();
        if (request()->wantsJson()){    
            return ['curriculum' => $curriculum];
        }
    }
    /**
     * Get achievements
     *
     * @param  \App\Curriculum  $curriculum
     * @return \Illuminate\Http\Response
     */
    public function getAchievements(Curriculum $curriculum)
    {
        abort_unless(\Gate::allows('curriculum_show'), 403);
        //check if user is enrolled or admin -> else 403 
        abort_unless((auth()->user()->curricula()->contains('id', $curriculum->id) // user enrolled
                  OR (auth()->user()->currentRole()->first()->id == 1)), 403);     // or admin
        $user_ids = request()->user_ids;
        
        $curriculum = Curriculum::with(['terminalObjectives', 
                        'terminalObjectives.achievements' => function($query) use ($user_ids) {
                                                            $query->whereIn('user_id', $user_ids);
                                                        },
                        'terminalObjectives.enablingObjectives', 
                        'terminalObjectives.enablingObjectives.achievements' => function($query) use ($user_ids) {
                                                            $query->whereIn('user_id', $user_ids);
                                                        },  
                        ])
                        ->find($curriculum->id);
        if (request()->wantsJson()){     
            return ['curriculum' => $curriculum];
        }
       
    }

    /**
     * Show curriculum in edit mode
     *
     * @param  \App\Curriculum  $curriculum
     * @return \Illuminate\Http\Response
     */
    public function edit(Curriculum $curriculum)
    {
        $grades             = Grade::all();
        $subjects           = Subject::all();
        $organization_types = OrganizationType::all();
        
        $countries = Country::all();
        $states = State::all();
        
        return view('curricula.edit')
                ->with(compact('grades'))
                ->with(compact('subjects'))
                ->with(compact('organization_types'))
                ->with(compact('countries'))
                ->with(compact('states'))
                ->with(compact('curriculum'));
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
        abort_unless(\Gate::allows('curriculum_edit'), 403);
        
        $input = $this->validateRequest();
        
        $curriculum->update([
            'title'                 => $input['title'],
            'description'           => $input['description'],
            'author'                => $input['author'],
            'publisher'             => $input['publisher'],
            'city'                  => $input['city'],
            'date'                  => $input['date'],
            'color'                 => $input['color'],
            'grade_id'              => format_select_input($input['grade_id']),
            'subject_id'            => format_select_input($input['subject_id']),
            'organization_type_id'  => format_select_input($input['organization_type_id']),
            'state_id'              => isset($input['state_id']) ? format_select_input($input['state_id']) : null, 
            'country_id'            => format_select_input($input['country_id']),
            'medium_id'             => $this->getMediumIdByInputFilepath($input),
            'owner_id'              => auth()->user()->id,
        ]);
        
        return redirect($curriculum->path());
    }
    
    /**
     * If $input['filepath'] is set and medium exists, id is return, else return is null
     * @param array $input
     * @return mixed
     */
    public function getMediumIdByInputFilepath($input){
        if (isset($input['filepath']))
        {
            $medium = new Medium();
            return (null !== $medium->getByFilemanagerPath($input['filepath'])) ? $medium->getByFilemanagerPath($input['filepath'])->id : null;
        } 
        else
        {
            return null;
        }
    }
    
    public function enrol()
    {
        abort_unless(\Gate::allows('course_create'), 403);
        
        foreach ((request()->enrollment_list) AS $enrolment)
        {  
            $return[] = Group::findOrFail($enrolment['group_id'])->curricula()->syncWithoutDetaching($enrolment['curriculum_id']); 
        }
        
        return $return;  
    }
    
     public function expel()
    {
        abort_unless(\Gate::allows('course_create'), 403);
        
        foreach ((request()->expel_list) AS $expel)
        {  
            $group = Group::find($expel['group_id']);
            $return[] = $group->curricula()->detach($expel['curriculum_id']);
        }
        
        return $return;  
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Curriculum  $curriculum
     * @return \Illuminate\Http\Response
     */
    public function destroy(Curriculum $curriculum)
    {
        abort_unless(\Gate::allows('curriculum_delete'), 403);
        $curriculum->delete();

        return back();
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
            'filepath'              => 'sometimes',
            'owner_id'              => 'sometimes',
            ]);
    }
}

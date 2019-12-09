<?php

namespace App\Http\Controllers;

use App\Curriculum;
use App\Course;
use App\User;
use App\TerminalObjective;
use App\EnablingObjective;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\ObjectiveType;



class CourseController extends Controller
{
    public function show(Course $course)
    {
        abort_unless(\Gate::allows('curriculum_show'), 403);        //check if user is enrolled or admin -> else 403 

        abort_unless((auth()->user()->curricula()->contains('id', $course->curriculum_id) // user enrolled
                  OR (auth()->user()->currentRole()->first()->id == 1)), 403);                // or admin

        $curriculum = Curriculum::with(['terminalObjectives', 
                        'terminalObjectives.media', 
                        'terminalObjectives.mediaSubscriptions', 
                        'terminalObjectives.referenceSubscriptions', 
                        'terminalObjectives.quoteSubscriptions', 
                        'terminalObjectives.achievements' => function($query) {
                            $query->where('user_id', auth()->user()->id);
                        },
                        'terminalObjectives.enablingObjectives', 
                        'terminalObjectives.enablingObjectives.media',
                        'terminalObjectives.enablingObjectives.mediaSubscriptions', 
                        'terminalObjectives.enablingObjectives.referenceSubscriptions', 
                        'terminalObjectives.enablingObjectives.quoteSubscriptions', 
                        'terminalObjectives.enablingObjectives.achievements' => function($query) {
                            $query->where('user_id', auth()->user()->id);
                        },        
                        'contentSubscriptions.content', 
                        'glossar.contents', 
                        'media'])
                        ->find($course->curriculum_id);                                                
        $objectiveTypes = ObjectiveType::all();
        $certificates   = \App\Certificate::all();
     
        $settings= json_encode([
            'edit' => false,
            'achievements' => true,
            'cross_reference_curriculum_id' => false
        ]);
        
        return view('curricula.show')
                ->with(compact('curriculum'))
                ->with(compact('objectiveTypes'))
                ->with(compact('course'))
                ->with(compact('certificates'))
                ->with(compact('settings'));
    }
    
    
    public function list()
    {
        $course = Course::where('id', request()->course_id)->get()->first();
        
        $users = User::select([
            'users.id', 
            'username', 
            'firstname', 
            'lastname', 
            'email',
            'email_verified_at',
            'status_id'
            ])
                ->join('group_user', 'users.id', '=', 'group_user.user_id')
                ->join('curriculum_group', 'curriculum_group.group_id', '=', 'group_user.group_id')
                ->where('curriculum_group.id', $course->id);
        
        return DataTables::of($users)
            ->addColumn('role', function ($users) {
                return $users->roles()->where('organization_id', auth()->user()->current_organization_id)->first()->title;                
            })
            ->addColumn('progress', function ($users) use ($course){
                return isset($users->progresses()
                                    ->where('referenceable_type', 'App\Curriculum')
                                    ->where('referenceable_id', $course->curriculum_id)
                                    ->first()->value) ? 
                             $users->progresses()
                                    ->where('referenceable_type', 'App\Curriculum')
                                    ->where('referenceable_id', $course->curriculum_id)
                                    ->first()->value : 0;                
            })
            ->addColumn('check', '')
            ->setRowId('id')
            ->setRowAttr([
                'color' => 'primary',
            ])
            ->make(true);
    }
}

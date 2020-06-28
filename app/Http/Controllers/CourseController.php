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
    public function index()
    {
        return view('home');
    }
    
    public function show(Course $course)
    {
        abort_unless(\Gate::allows('curriculum_show'), 403);        //check if user is enrolled or admin -> else 403 

        abort_unless((auth()->user()->curricula()->contains('id', $course->curriculum_id) // user enrolled
                  OR (auth()->user()->currentRole()->first()->id == 1)), 403);                // or admin

        $curriculum = Curriculum::with(['terminalObjectives', 
                        //'terminalObjectives.media', 
//                        'terminalObjectives.mediaSubscriptions', 
//                        'terminalObjectives.referenceSubscriptions', 
//                        'terminalObjectives.quoteSubscriptions', 
                        'terminalObjectives.achievements' => function($query) {
                            $query->where('user_id', auth()->user()->id);
                        },
                        'terminalObjectives.enablingObjectives', 
                        //'terminalObjectives.enablingObjectives.media',
                        //'terminalObjectives.enablingObjectives.mediaSubscriptions', 
                        //'terminalObjectives.enablingObjectives.referenceSubscriptions', 
                        //'terminalObjectives.enablingObjectives.quoteSubscriptions', 
                        'terminalObjectives.enablingObjectives.achievements' => function($query) {
                            $query->where('user_id', auth()->user()->id);
                        },        
                        //'contentSubscriptions.content', 
                        //'glossar.contents', 
                        //'media'
                        ])
                        ->find($course->curriculum_id);                                                
        $objectiveTypes = ObjectiveType::all();
        $certificates   = \App\Certificate::all();
        $logbook        = (null !==  $course->logbookSubscription()->get()->first()) ? $course->logbookSubscription()->get()->first()->logbook()->get()->first() : null;
        
        $settings= json_encode([
            'course' => true,
            'edit' => false,
            'achievements' => true,
            'cross_reference_curriculum_id' => false
        ]);
        
        return view('curricula.show')
                ->with(compact('curriculum'))
                ->with(compact('objectiveTypes'))
                ->with(compact('course'))
                ->with(compact('certificates'))
                ->with(compact('logbook'))
                ->with(compact('settings'));
    }
    
    
    public function list()
    {
         abort_unless(\Gate::allows('curriculum_show'), 403);                            //check if user is enrolled or admin -> else 403 
         abort_unless((auth()->user()
                            ->with(['groups.courses' => function($query) {               //user enrolled
                                     $query->where('id', request()->course_id);
                            }])                                 
                            OR (auth()->user()->currentRole()->first()->id == 1)), 403); // or admin

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
            ->join('organization_role_users', 'organization_role_users.user_id', '=', 'group_user.user_id')
            ->where('group_user.group_id', '=', $course->group_id)
            ->where('organization_role_users.organization_id', '=', auth()->user()->current_organization_id)
            ->where('organization_role_users.role_id', '=', 6);
        
       return empty($users) ? null : DataTables::of($users)
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
            ->make(true);
    }
}

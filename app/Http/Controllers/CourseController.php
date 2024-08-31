<?php

namespace App\Http\Controllers;

use App\Certificate;
use App\Course;
use App\CurriculumSubscription;
use App\Curriculum;
use App\ObjectiveType;
use App\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(\Gate::allows('curriculum_show'), 403);
        $input = $this->validateRequest();

        $courses= CurriculumSubscription::where('subscribable_type', "App\Group")
            ->where('subscribable_id', $input['group_id'])
            ->with(['curriculum']);

        return empty($courses) ? '' : DataTables::of($courses)
            ->addColumn('title', function ($courses) {
                return $courses->curriculum->title;
            })
            ->addColumn('description', function ($courses) {
                return $courses->curriculum->description;
            })
            ->addColumn('medium_id', function ($courses) {
                return $courses->curriculum->medium_id;
            })
            ->setRowId('id')
            ->setRowAttr([
                'color' => 'primary',
            ])
            ->make(true);

    }

    public function show(Course $course)
    {
        abort_unless((\Gate::allows('curriculum_show') and $course->isAccessible()), 403);        //check if user is enrolled or admin -> else 403

        LogController::set(get_class($this).'@'.__FUNCTION__, $course->curriculum_id);

        $curriculum = Curriculum::with([
            'terminalObjectives',
            'terminalObjectives.media',
            'terminalObjectives.mediaSubscriptions',
            'terminalObjectives.achievements' => function ($query) {
                $query->where('user_id', auth()->user()->id);
            },
            'terminalObjectives.enablingObjectives',
            'terminalObjectives.enablingObjectives.media',
            'terminalObjectives.enablingObjectives.mediaSubscriptions',
            'terminalObjectives.enablingObjectives.achievements' => function ($query) {
                $query->where('user_id', auth()->user()->id);
            },
            'contentSubscriptions.content',
            'glossar.contents',
            'media',
        ])
                        ->find($course->curriculum_id);
        $objectiveTypes = ObjectiveType::all();
        $certificates = Certificate::where([
            ['curriculum_id', '=', $course->curriculum_id],
            ['organization_id', '=', auth()->user()->current_organization_id],
        ])
                        ->orWhere([
                            ['curriculum_id', '=', $course->curriculum_id],
                            ['global', '=', 1],
                        ])
                        ->orWhere([
                            ['type', '=', 'group'],
                            ['global', '=', 1],
                        ])
                        ->get();
        /*$logbook = (null !== $course->logbookSubscription()->get()->first()) ? $course->logbookSubscription()->get()->first()->logbook()->get()->first() : null;*/

        $settings = json_encode([
            'course' => true,
            'edit' => false,
            'achievements' => true,
            'cross_reference_curriculum_id' => false,
        ]);

        return view('curricula.show')
                ->with(compact('curriculum'))
                ->with(compact('objectiveTypes'))
                ->with(compact('course'))
                ->with(compact('certificates'))
                /*->with(compact('logbook'))*/
                ->with(compact('settings'));
    }

    public function list()
    {
        $request = $this->validateRequest();
        abort_unless(\Gate::allows('curriculum_show'), 403);                            //check if user is enrolled or admin -> else 403
        abort_unless((auth()->user()
                            ->with(['groups.courses' => function ($query, $request) {               //user enrolled
                                $query->where('id', $request['course_id']);
                            }])
                            or (auth()->user()->currentRole()->first()->id == 1)), 403); // or admin

        $course = CurriculumSubscription::where('id', $request['course_id'])->get()->first();


        $users = User::select([
            'users.id',
            'username',
            'firstname',
            'lastname',
            'email',
            'email_verified_at',
            'status_id',
        ])
            ->join('group_user', 'users.id', '=', 'group_user.user_id')
            ->join('organization_role_users', 'organization_role_users.user_id', '=', 'group_user.user_id')
            ->where('group_user.group_id', '=', $course->subscribable_id)
            ->where('organization_role_users.organization_id', '=', auth()->user()->current_organization_id)
            ->where('organization_role_users.role_id', '=', 6)
            ->with(['progresses' => function ($query) use ($course) {
            $query->where('referenceable_type', 'App\Curriculum')
                  ->where('referenceable_id', $course->curriculum_id);
        }]);

        return empty($users) ? null : DataTables::of($users)
            ->addColumn('role', function ($users) {
                return $users->roles()->where('organization_id', auth()->user()->current_organization_id)->first()->title;
            })
     //    ->addColumn('progress', isset($users->progresses) ? $users->progresses->first()->value : 0)
            ->addColumn('progress',
                    function ($users) use ($course) {
                        return isset($users->progresses()
                                    ->where('referenceable_type', 'App\Curriculum')
                                    ->where('referenceable_id', $course->curriculum_id)
                                    ->first()->value) ?
                             $users->progresses()
                                    ->where('referenceable_type', 'App\Curriculum')
                                    ->where('referenceable_id', $course->curriculum_id)
                                    ->first()->value : 0;
                    })
            ->setRowId('id')
            ->make(true);
    }

    protected function validateRequest()
    {
        return request()->validate([
            'group_id' => 'sometimes|integer',
            'course_id' => 'sometimes|integer',
        ]);
    }
}

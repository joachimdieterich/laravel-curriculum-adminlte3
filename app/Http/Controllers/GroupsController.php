<?php

namespace App\Http\Controllers;

use App\Curriculum;
use App\CurriculumSubscription;
use App\Grade;
use App\Group;
use App\Http\Requests\MassDestroyGroupRequest;
use App\Http\Requests\UpdateGroupRequest;
use App\Organization;
use App\OrganizationRoleUser;
use App\Period;
use App\User;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;

class GroupsController extends Controller
{
    public function index()
    {
        // select2 request
        if (request()->wantsJson() ) {
            return $this->getEntriesForSelect2();
        }
        // select2 should return entries for students
        abort_unless(\Gate::allows('group_access'), 403);
        return view('groups.index');
    }

    public function list()
    {
        abort_unless(\Gate::allows('group_access'), 403);

        $group_id_field = 'id'; // if auth()->user()->groups() is used query uses group_user table therefore group_id_field = group_id

        switch (auth()->user()->role()->id) {
            case 1: // admin
                $groups = Group::with(['grade', 'period', 'organization']);
                break;
            case 2: // creator
            case 4: // schooladmin
            case 5: // teacher
                $groups = Group::where('organization_id', auth()->user()->current_organization_id)->with(['grade', 'period', 'organization']);
                break;
            default: // student
                // needs get() because DataTables will try to order by 'id', but because of the 'join group_user' it should be 'groups.id'
                $groups = auth()->user()->groups()->with(['grade', 'period', 'organization'])->get();
                break;
        }

       /* $show_gate = \Gate::allows('group_show');
        $edit_gate = \Gate::allows('group_edit');
        $delete_gate = \Gate::allows('group_delete');*/

        return DataTables::of($groups)
            ->addColumn('grade', function ($groups) {
                return $groups->grade->title;
            })
            ->addColumn('period', function ($groups) {
                return $groups->period->title;
            })
            ->addColumn('organization', function ($groups) {
                return $groups->organization->title;
            })
            ->setRowId($group_id_field)
            ->make(true);
    }


    public function store()
    {
        abort_unless(\Gate::allows('group_create'), 403);
        $new_group = $this->validateRequest();

        $group = Group::firstOrCreate([
            'title' => $new_group['title'],
            'common_name' => $new_group['common_name'] ?? null,
            'grade_id' => format_select_input($new_group['grade_id']),
            'period_id' => format_select_input($new_group['period_id']),
            'organization_id' => format_select_input($new_group['organization_id']),
        ]);

        if (request()->wantsJson()) {
            return $group;
        }

    }

    /**
     * Display the specified group.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Group $group)
    {
        abort_unless((\Gate::allows('group_show') and $group->isAccessible()), 403);

        LogController::set(get_class($this).'@'.__FUNCTION__, $group->id);

        if (request()->wantsJson()) {
            return ['users' => json_encode($group->users)];
        }

        $group = Group::where('id', $group->id)->with('glossar')->get()->first();

        return view('groups.show')
                ->with(compact('group'));
    }


    public function update(UpdateGroupRequest $request, Group $group)
    {
        abort_unless((\Gate::allows('group_edit') and $group->isAccessible()), 403);

        $group->update([
            'title' => $request['title'],
            'common_name' => $request['common_name'] ?? $group->common_name,
            'grade_id' => format_select_input($request['grade_id']),
            'period_id' => format_select_input($request['period_id']),
            'organization_id' => format_select_input($request['organization_id']),
        ]);

        return $group;
    }

    /**
     * Remove the specified group from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Group $group)
    {
        abort_unless((\Gate::allows('group_delete') and $group->isAccessible()), 403);

        // first delete all relations
        CurriculumSubscription::where([
            'subscribable_type' => "App\Group",
            'subscribable_id' => $group->id,
        ])->delete();

        //$group->curricula()->detach();
        $group->users()->detach();

        //todo: delete subscriptions ( eg. kanban), yet no relation in Group.php

        return $group->delete();
    }

    public function massDestroy(MassDestroyGroupRequest $request)
    {
        abort_unless(\Gate::allows('group_delete'), 403);

        foreach (request('ids') as $id) {
            abort_unless(
                (
                    auth()->user()->groups->contains($id)
                    or is_admin()
                    or (Group::find($id)->first()->organization_id == auth()->user()->current_organization_id)
                ), 403);

            Group::where('id', $id)->delete();
        }

        return response(null, 204);
    }

    public function enrol()
    {
        abort_unless(\Gate::allows('group_enrolment'), 403, "No permission to enrol users into groups");

        foreach ((request()->enrollment_list) as $enrolment) {
            abort_unless((
                is_admin()
                or auth()->user()->groups->contains($enrolment['group_id']) // user is enrolled in group
                or Group::find($enrolment['group_id'])->organization_id == auth()->user()->current_organization_id // current set organization is group's organization
            ), 403, "Need to be enroled in group or have its organization be set as current organization");

            foreach ((array) $enrolment['user_id'] as $user_id) // iterate over user_ids
            {
                $user = User::findOrFail(format_select_input($user_id));
                foreach ((array) $enrolment['group_id'] as $group_id) // iterate over group_ids
                {
                    $group = Group::findOrFail($group_id);
                    // if user isn't enrolled to organization, enrol with student role
                    OrganizationRoleUser::firstOrCreate([
                            'user_id' => $user->id,
                            'organization_id' => $group->first()->organization_id,
                        ],
                        [
                            'role_id' => 6
                        ]
                    );

                    $user->groups()->syncWithoutDetaching($group->id);
                }
            }
        }

        if (request()->wantsJson()) {
            return $user ?? false;
        }
    }

    public function expel()
    {
        abort_unless(\Gate::allows('group_enrolment'), 403, "No permission to remove users from groups");

        foreach ((request()->expel_list) as $expel) {
            abort_unless((
                is_admin()
                or auth()->user()->groups->contains($expel['group_id']) // user is enrolled in group
                or Group::find($expel['group_id'])->organization_id == auth()->user()->current_organization_id // current set organization is group's organization
            ), 403, "Need to be enroled in group or have its organization be set as current organization");

            foreach ((array) $expel['user_id'] as $user_id)
            {
                $user = User::find($user_id);
                $return[] = $user->groups()->detach($expel['group_id']);
            }
        }

        if (request()->wantsJson()) {
            return $user ?? false; // todo: return multiple users if array is given
        }
    }

    protected function select2RequestWithOptGroup($collection, $field = 'title' )
    {
        $input = request()->validate([
            'page' => 'sometimes|integer',
            'term' => 'sometimes|string|max:255|nullable',
            'selected' => 'sometimes|nullable',
        ]);
        if (request()->has('selected'))
        {
            //dump($input['selected']);
            //dump($model::whereIn($id, (array)$input['selected'])->get());
            return response()->json(Group::whereIn('id', explode(",", $input['selected']))->get()); //todo check for multiple selects
        }
        else
        {
            $page = $input['page'];
            $term = $input['term'];

            $resultCount = 25;
            $offset = ($page - 1) * $resultCount;

            $count = count($collection->where(
                function($query) use ($field, $term)
                {
                    $query->whereHas('groups', function ($query) use ($term) {
                        $query->where('title', 'like', '%' . $term . '%'); }
                    );
                })
                ->get());

            $entries = $collection->where(
                function($query) use ($field, $term)
                {
                    $query->whereHas('groups', function ($query) use ($term) {
                        $query->where('title', 'like', '%' . $term . '%'); }
                    );
                })
                ->orderBy('title')
                ->skip($offset)
                ->take($resultCount)
                ->select(['organizations.id', DB::raw('title as text')])
                ->get();

            $endCount = $offset + $resultCount;
            $morePages = $count > $endCount;

            $select2entries = array();
            foreach($entries as $entry){
                $select2entries[] = array(
                    "text" => $entry['text'],
                    "children" => Group::where('organization_id', $entry['id'])
                        ->select(['id', DB::raw('title as text')])
                        ->where('title', 'like', '%' . $term . '%')
                        ->get()
                );
            }
            $results = array(
                "results" => $select2entries,
                "pagination" => array(
                    "more" => $morePages
                )
            );

            return response()->json($results);
        }
    }

    protected function validateRequest()
    {
        return request()->validate([
            'title'             => 'sometimes|required',
            'grade_id'          => 'sometimes',
            'period_id'         => 'sometimes',
            'organization_id'   => 'sometimes',
            'common_name'       => 'sometimes',
        ]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    private function getEntriesForSelect2(): \Illuminate\Http\JsonResponse
    {
       /* if (is_admin()) {
            return $this->select2RequestWithOptGroup(
                Organization::select(['id', 'title'])
            );
        } else*/if (is_admin() OR is_schooladmin() OR is_teacher()) {
            return $this->select2RequestWithOptGroup(
                auth()->user()->organizations()->select(['organizations.id', 'organizations.title'])
                //Organization::where('id', auth()->user()->current_organization_id)->select(['id', 'title'])
            ); //todo: search/filter not working for schooladmins
        } else {
            return getEntriesForSelect2ByCollection(
                auth()->user()->groups()->orderBy('organization_id', 'desc'),
                'groups.'
            );
        }
    }
}

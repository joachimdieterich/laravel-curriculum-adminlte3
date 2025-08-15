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
        abort_unless(\Gate::allows('group_access'), 403);
        // select2 request
        if (request()->wantsJson() AND request()->has(['term', 'page'])) {
            return $this->getEntriesForSelect2();
        }

        if (request()->wantsJson()) {
            return ['groups' => json_encode(auth()->user()->groups)];
        }

        return view('groups.index');
    }

    public function list()
    {
        abort_unless(\Gate::allows('group_access'), 403);

        $group_id_field = 'id'; // if auth()->user()->groups() is used query uses group_user table therefore group_id_field = group_id

        switch (auth()->user()->role()->id) {
            case 1:  $groups = Group::with(['grade', 'period', 'organization']);
                break;
            case 4: // Schooladmins and Teachers should only see groups of current organization
            case 5:  $groups = Group::where('organization_id', auth()->user()->current_organization_id)->with(['grade', 'period', 'organization']);
                break;

            default: $groups = auth()->user()->groups()->with(['grade', 'period', 'organization']);
                $group_id_field = 'group_id';
                break;
        }

        $show_gate = \Gate::allows('group_show');
        $edit_gate = \Gate::allows('group_edit');
        $delete_gate = \Gate::allows('group_delete');

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
            ->addColumn('action', function ($groups) use ($show_gate, $edit_gate, $delete_gate, $group_id_field) {
                $actions = '';
                if ($show_gate) {
                    $actions .= '<a href="'.route('groups.show', $groups->$group_id_field).'" '
                        .'id="show-group-'.$groups->$group_id_field.'" '
                        .'class="btn p-1">'
                        .'<i class="fa fa-list-alt"></i>'
                        .'</a>';
                }
                if ($edit_gate) {
                    $actions .= '<a href="'.route('groups.edit', $groups->$group_id_field).'" '
                        .'id="edit-group-'.$groups->$group_id_field.'" '
                        .'class="btn p-1">'
                        .'<i class="fa fa-pencil-alt"></i> '
                        .'</a>';
                }
                if ($delete_gate) {
                    $actions .= '<button type="button" '
                        .'class="btn text-danger" '
                        .'onclick="destroyDataTableEntry(\'groups\','.$groups->$group_id_field.')">'
                        .'<i class="fa fa-trash"></i></button>';
                }

                return $actions;
            })

            ->addColumn('check', '')
            ->setRowId($group_id_field)
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_unless(\Gate::allows('group_create'), 403);

        $grades = Organization::where('id', auth()->user()->current_organization_id)->get()->first()->type->grades()->get(); //Grade::all();
        $periods = Period::all(); //Organization::where('id',auth()->user()->current_organization_id)->get()->first()->periods;
        $organizations = (auth()->user()->role()->id == 1) ? Organization::all() : auth()->user()->organizations()->get();

        return view('groups.create')
                ->with(compact('grades'))
                ->with(compact('periods'))
                ->with(compact('organizations'));
    }

    public function store()
    {
        abort_unless(\Gate::allows('group_create'), 403);
        $new_group = $this->validateRequest();

        $group = Group::firstOrCreate([
            'title' => $new_group['title'],
            'grade_id' => format_select_input($new_group['grade_id']),
            'period_id' => format_select_input($new_group['period_id']),
            'organization_id' => format_select_input($new_group['organization_id']),
        ]);

        // axios call?
        if (request()->wantsJson()) {
            return ['message' => $group->path()];
        }

        return redirect($group->path());
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
        // axios call?
        if (request()->wantsJson()) {
            return ['users' => json_encode($group->users)];
        }

        $group = Group::where('id', $group->id)->with('glossar')->get()->first();

        return view('groups.show')
                ->with(compact('group'));
    }

    public function edit(Group $group)
    {
        abort_unless((\Gate::allows('group_edit') and $group->isAccessible()), 403);

        $grades = Organization::where('id', auth()->user()->current_organization_id)->get()->first()->type->grades()->get(); //Grade::all();
        $periods = Period::all(); //Organization::where('id',auth()->user()->current_organization_id)->get()->first()->periods;
        $organizations = (auth()->user()->role()->id == 1) ? Organization::all() : auth()->user()->organizations()->get();

        return view('groups.edit')
                ->with(compact('group'))
                ->with(compact('grades'))
                ->with(compact('periods'))
                ->with(compact('organizations'));
    }

    public function update(UpdateGroupRequest $request, Group $group)
    {
        abort_unless((\Gate::allows('group_edit') and $group->isAccessible()), 403);

        $group->update([
            'title' => $request['title'],
            'grade_id' => format_select_input($request['grade_id']),
            'period_id' => format_select_input($request['period_id']),
            'organization_id' => format_select_input($request['organization_id']),
        ]);

        return redirect()->route('groups.index');
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

        $group->delete();

        return back();
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
        abort_unless(\Gate::allows('group_enrolment'), 403);

        foreach ((request()->enrollment_list) as $enrolment) {
            abort_unless(
                (
                    auth()->user()->groups->contains($enrolment['group_id'])
                    or is_admin()
                    or (Group::find($enrolment['group_id'])->first()->organization_id == auth()->user()->current_organization_id)
                ), 403);

            $group = Group::findOrFail($enrolment['group_id']);
            $user = User::findOrFail($enrolment['user_id']);
            //if user isn't enrolled to organization, enrol with student role
            OrganizationRoleUser::firstOrCreate(['user_id' => $user->id,
                'organization_id' => $group->first()->organization_id, ],
                ['role_id' => 6]
            );

            $return[] = $user->groups()->syncWithoutDetaching($enrolment['group_id']);
        }

        if (request()->wantsJson()) {
            return ['users' => $group->users];
        } else {
            return $return;
        }
    }

    public function expel()
    {
        abort_unless(\Gate::allows('group_enrolment'), 403);

        foreach ((request()->expel_list) as $expel) {
            abort_unless((auth()->user()->groups->contains($expel['group_id'])
                or is_admin()
                or (Group::find($expel['group_id'])->first()->organization_id == auth()->user()->current_organization_id)), 403);

            $user = User::find($expel['user_id']);
            $return[] = $user->groups()->detach($expel['group_id']);
        }

        if (request()->wantsJson()) {
            return ['users' => Group::findOrFail($expel['group_id'])->users];
        } else {
            return $return;
        }
    }

    protected function select2RequestWithOptGroup($collection, $field = 'title' )
    {
        $input = request()->validate([
            'page' => 'required|integer',
            'term' => 'sometimes|string|max:255|nullable',
        ]);
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

    protected function validateRequest()
    {
        return request()->validate([
            'title'             => 'sometimes|required',
            'grade_id'          => 'sometimes',
            'period_id'         => 'sometimes',
            'organization_id'   => 'sometimes',
        ]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    private function getEntriesForSelect2(): \Illuminate\Http\JsonResponse
    {
        if (is_admin()) {
            return $this->select2RequestWithOptGroup(
                Organization::select(['id', 'title'])
            );
        } elseif (is_schooladmin() OR is_teacher()) {
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

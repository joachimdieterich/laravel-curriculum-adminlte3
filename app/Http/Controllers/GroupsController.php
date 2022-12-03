<?php

namespace App\Http\Controllers;

use App\Curriculum;
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

        switch (auth()->user()->role()->id) {
            case 1:  $groups = Group::with(['grade', 'period', 'organization'])->get();
                break;
            case 4:  $groups = Group::where('organization_id', auth()->user()->current_organization_id)->with(['grade', 'period', 'organization'])->get();
                break;

            default: $groups = auth()->user()->groups()->with(['grade', 'period', 'organization'])->get();
                break;
        }
        //$groups = (auth()->user()->role()->id == 1) ? Group::all() : auth()->user()->groups()->get();

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
            ->addColumn('action', function ($groups) use ($show_gate, $edit_gate, $delete_gate) {
                $actions = '';
                if ($show_gate) {
                    $actions .= '<a href="'.route('groups.show', $groups->id).'" '
                                    .'id="show-group-'.$groups->id.'" '
                                    .'class="btn p-1">'
                                    .'<i class="fa fa-list-alt"></i>'
                                    .'</a>';
                }
                if ($edit_gate) {
                    $actions .= '<a href="'.route('groups.edit', $groups->id).'" '
                                    .'id="edit-group-'.$groups->id.'" '
                                    .'class="btn p-1">'
                                    .'<i class="fa fa-pencil-alt"></i> '
                                    .'</a>';
                }
                if ($delete_gate) {
                    $actions .= '<button type="button" '
                                .'class="btn text-danger" '
                                .'onclick="destroyDataTableEntry(\'groups\','.$groups->id.')">'
                                .'<i class="fa fa-trash"></i></button>';
                }

                return $actions;
            })

            ->addColumn('check', '')
            ->setRowId('id')
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

        /*  abort_unless((auth()->user()->groups->contains($group)
              OR is_admin()
              OR ($group->organization_id == auth()->user()->current_organization_id)), 403);*/

        LogController::set(get_class($this).'@'.__FUNCTION__, $group->id);
        // axios call?
        if (request()->wantsJson()) {
            // dump(json_encode($group->users));
            return ['users' => json_encode($group->users)];
        }
        $courses = $group->courses()->with('curriculum')->get();
        $group = Group::where('id', $group->id)->with('glossar')->get()->first();

        return view('groups.show')
                ->with(compact('group'))
                ->with(compact('courses'));
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
        $group->curricula()->detach();
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
        $resultCount = 25;

        $offset = ($page - 1) * $resultCount;

        $term = $input['term'];

        $count = Count($collection->has('groups')->where(
            function($query) use ($field, $term)
            {
                foreach ((array) $field as $f) {
                    $query->whereHas('groups', function ($query) use ($term) {
                        $query->where('title', 'like', $term.'%'); }
                    );
                    $query->orWhere($f, 'LIKE', '%' . $term . '%');
                }
            })
            ->orderBy('title')
            ->select(['id', DB::raw('title as text')])
            ->get());


        $entries = $collection->has('groups')->where(
            function($query) use ($field, $term)
            {
                foreach ((array) $field as $f) {
                    $query->whereHas('groups', function ($query) use ($term) {
                        $query->where('title', 'like', $term.'%'); }
                    );
                    $query->orWhere($f, 'LIKE', '%' . $term . '%');
                }
            })
            ->orderBy('title')
            ->skip($offset)
            ->take($resultCount)
            ->select(['id', DB::raw('title as text')])
            ->get();

        $endCount = $offset + $resultCount;
        $morePages = $count > $endCount;

        $select2entries = array();
        foreach($entries as $entry){
            $select2entries[] = array(
                "text" => $entry['text'],
                "children" => Group::where('organization_id', $entry['id'])
                    ->select(['id', DB::raw('title as text')])
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
        } elseif (is_schooladmin()) {
            return $this->select2RequestWithOptGroup(
                Organization::where('id', auth()->user()->current_organization_id)->select(['id', 'title'])
            );
        } else {
            return getEntriesForSelect2ByCollection(
                auth()->user()->groups()->orderBy('organization_id', 'desc'),
                'groups.'
            );
        }
    }
}

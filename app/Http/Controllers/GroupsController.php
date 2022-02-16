<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Group;
use App\User;
use App\OrganizationRoleUser;
use App\Grade;
use App\Period;
use App\Curriculum;
use App\Organization;
use App\Http\Requests\MassDestroyGroupRequest;
use App\Http\Requests\UpdateGroupRequest;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;

class GroupsController extends Controller
{
    public function index()
    {
        abort_unless(\Gate::allows('group_access'), 403);

        if (request()->wantsJson()){
            return ['groups' => json_encode(auth()->user()->groups)];
        } else {
            $curricula = (is_admin()) ? Curriculum::all() : Curriculum::where('type_id', 1)->get();
            if (is_schooladmin()) //schooladmin should see all curricula of users of current organizations
            {
                $curricula = $curricula->merge(Curriculum::whereIn('owner_id', Organization::where('id', auth()->user()->current_organization_id)->first()->users()->pluck('id')->toArray())->get());
            }
            return view('groups.index')
                ->with(compact('curricula'));
        }
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
                 $actions  = '';
                    if ($show_gate){
                        $actions .= '<a href="'.route('groups.show', $groups->id).'" '
                                    . 'id="show-group-'.$groups->id.'" '
                                    . 'class="btn p-1">'
                                    . '<i class="fa fa-list-alt"></i>'
                                    . '</a>';
                    }
                    if ($edit_gate){
                        $actions .= '<a href="'.route('groups.edit', $groups->id).'" '
                                    . 'id="edit-group-'.$groups->id.'" '
                                    . 'class="btn p-1">'
                                    . '<i class="fa fa-pencil-alt"></i> '
                                    . '</a>';
                    }
                    if ($delete_gate){
                        $actions .= '<button type="button" '
                                . 'class="btn text-danger" '
                                . 'onclick="destroyDataTableEntry(\'groups\','.$groups->id.')">'
                                . '<i class="fa fa-trash"></i></button>';
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

        $grades  = Organization::where('id', auth()->user()->current_organization_id)->get()->first()->type->grades()->get(); //Grade::all();
        $periods = Period::all();//Organization::where('id',auth()->user()->current_organization_id)->get()->first()->periods;
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
            'organization_id' => format_select_input($new_group['organization_id'])
        ]);

        // axios call?
        if (request()->wantsJson()){
            return ['message' => $group->path()];
        }

        return redirect($group->path());
    }

    /**
     * Display the specified group.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Group $group)
    {
        abort_unless((\Gate::allows('group_show') and $group->isAccessible()), 403);

        /*  abort_unless((auth()->user()->groups->contains($group)
              OR is_admin()
              OR ($group->organization_id == auth()->user()->current_organization_id)), 403);*/

        LogController::set(get_class($this) . '@' . __FUNCTION__, $group->id);
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

        $grades  = Organization::where('id', auth()->user()->current_organization_id)->get()->first()->type->grades()->get(); //Grade::all();
        $periods = Period::all();//Organization::where('id',auth()->user()->current_organization_id)->get()->first()->periods;
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

        foreach (request('ids') AS $id) {
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

        foreach ((request()->enrollment_list) AS $enrolment) {
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
                'organization_id' => $group->first()->organization_id],
                ['role_id' => 6]
            );

            $return[] = $user->groups()->syncWithoutDetaching($enrolment['group_id']);
        }

        if (request()->wantsJson()){
            return ['users' => $group->users];
        } else {
            return $return;
        }

    }

    public function expel()
    {
        abort_unless(\Gate::allows('group_enrolment'), 403);

        foreach ((request()->expel_list) AS $expel)
        {
            abort_unless((auth()->user()->groups->contains($expel['group_id'])
                OR is_admin()
                OR (Group::find($expel['group_id'])->first()->organization_id == auth()->user()->current_organization_id)), 403);

            $user = User::find($expel['user_id']);
            $return[] = $user->groups()->detach($expel['group_id']);
        }

        if (request()->wantsJson()){
            return ['users' => Group::findOrFail($expel['group_id'])->users];
        } else {
            return $return;
        }
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
}

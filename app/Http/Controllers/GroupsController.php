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

        $curricula =  (auth()->user()->role()->id == 1) ? Curriculum::all() : auth()->user()->curricula();

        return view('groups.index')
          ->with(compact('curricula'));
    }
    
    public function list()
    {
        abort_unless(\Gate::allows('group_access'), 403);
        $groups = (auth()->user()->role()->id == 1) ? Group::all() : auth()->user()->groups()->get();      
        
        return DataTables::of($groups)
            ->addColumn('grade', function ($groups) {
                return $groups->grade()->first()->title;                
            })
            ->addColumn('period', function ($groups) {
                return $groups->period()->first()->title;                
            })
            ->addColumn('organization', function ($groups) {
                return $groups->organization()->first()->title;                
            })
            ->addColumn('action', function ($groups) {
                 $actions  = '';
                    if (\Gate::allows('group_show')){
                        $actions .= '<a href="'.route('groups.show', $groups->id).'" '
                                    . 'id="show-group-'.$groups->id.'" '
                                    . 'class="btn p-1">'
                                    . '<i class="fa fa-list-alt"></i>'
                                    . '</a>';
                    }
                    if (\Gate::allows('group_edit')){
                        $actions .= '<a href="'.route('groups.edit', $groups->id).'" '
                                    . 'id="edit-group-'.$groups->id.'" '
                                    . 'class="btn p-1">'
                                    . '<i class="fa fa-edit"></i> '
                                    . '</a>';
                    }
                    if (\Gate::allows('group_delete')){
                        $actions .= '<form action="'.route('groups.destroy', $groups->id).'" method="POST" class="pull-right">'
                                    . '<input type="hidden" name="_method" value="delete">'. csrf_field().''
                                    . '<button '
                                    . 'type="submit" '
                                    . 'id="delete-group-'.$groups->id.'" '
                                    . 'class="btn text-danger p-1"><i class="fa fa-trash"></i> ';
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
        abort_unless(\Gate::allows('group_create'), 403);
        
        $grades  = Organization::where('id', auth()->user()->current_organization_id)->get()->first()->type->grades()->get(); //Grade::all();
        $periods = Organization::where('id',auth()->user()->current_organization_id)->get()->first()->periods;
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
        abort_unless(\Gate::allows('group_access'), 403);
        return view('groups.show')
                ->with(compact('group'));
    }
    
    public function edit(Group $group)
    {
        abort_unless(\Gate::allows('group_edit'), 403);
        
        $grades  = Organization::where('id', auth()->user()->current_organization_id)->get()->first()->type->grades()->get(); //Grade::all();
        $periods = Organization::where('id',auth()->user()->current_organization_id)->get()->first()->periods;
        $organizations = (auth()->user()->role()->id == 1) ? Organization::all() : auth()->user()->organizations()->get();
        
        return view('groups.edit')
                ->with(compact('group'))
                ->with(compact('grades'))
                ->with(compact('periods'))
                ->with(compact('organizations'));
    }
    
    
    public function update(UpdateGroupRequest $request, Group $group)
    {
        abort_unless(\Gate::allows('group_edit'), 403);
        
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
        abort_unless(\Gate::allows('group_delete'), 403);

        $group->delete();

        return back();
    }
    
    public function massDestroy(MassDestroyGroupRequest $request)
    {
        abort_unless(\Gate::allows('group_delete'), 403);
        Group::whereIn('id', request('ids'))->delete();

        return response(null, 204);
    }
    
    public function enrol()
    {
        abort_unless(\Gate::allows('group_create'), 403);

        foreach ((request()->enrollment_list) AS $enrolment)
        {  
            $group = Group::findOrFail($enrolment['group_id']);
            $user = User::findOrFail($enrolment['user_id']);
            //if user isn't enrolled to organization, enrol with student role
            OrganizationRoleUser::firstOrCreate(['user_id' => $user->id,
                                         'organization_id' => $group->first()->organization_id],
                                         ['role_id'        => 6]
                                    );
           
            $return[] = $user->groups()->syncWithoutDetaching($enrolment['group_id']);
           
        }
        
        return $return;  
    }
    
    public function expel()
    {
        abort_unless(\Gate::allows('group_create'), 403);
        
        foreach ((request()->expel_list) AS $expel)
        {  
            $user = User::find($expel['user_id']);
            $return[] = $user->groups()->detach($expel['group_id']);
        }
        
        return $return;  
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

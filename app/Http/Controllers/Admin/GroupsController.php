<?php

namespace App\Http\Controllers\Admin;

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

class GroupsController extends Controller
{
    public function index()
    {
        abort_unless(\Gate::allows('group_access'), 403);

        $curricula = Curriculum::all();

        return view('admin.groups.index')
          ->with(compact('curricula'));
    }
    
    public function list()
    {
        $groups = Group::select([
            'id', 
            'title', 
            'grade_id', 
            'period_id', 
            'organization_id',
            'owner_id',
            ]);       
        
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
            ->addColumn('owner', function ($groups) {
                return $groups->owner()->first()->fullName();                
            })
            ->addColumn('action', function ($groups) {
                 $actions  = '';
                    if (\Gate::allows('group_show')){
                        $actions .= '<a href="'.route('admin.groups.show', $groups->id).'" '
                                    . 'class="btn btn-xs btn-success mr-1">'
                                    . '<i class="fa fa-list-alt"></i> '.trans('global.show').''
                                    . '</a>';
                    }
                    if (\Gate::allows('group_edit')){
                        $actions .= '<a href="'.route('admin.groups.edit', $groups->id).'" '
                                    . 'class="btn btn-xs btn-primary mr-1">'
                                    . '<i class="fa fa-edit"></i> '.trans('global.edit').''
                                    . '</a>';
                    }
                    if (\Gate::allows('group_delete')){
                        $actions .= '<form action="'.route('admin.groups.destroy', $groups->id).'" method="POST">'
                                    . '<input type="hidden" name="_method" value="delete">'. csrf_field().''
                                    . '<button type="submit" class="btn btn-xs btn-danger"><i class="fa fa-trash"></i> '.trans('global.delete').'</button>';
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
    
//    public function getGroupSelect($id, $dependency = 'id', $orderBy = 'title', $order = 'desc')
//    {
//        $group = Group::where($dependency, $id)
//               ->orderBy($orderBy, $order)
//               ->get();
//        
//        return compact($group);
//    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $grades = Grade::all();
        $periods = Period::all();
        $organizations = Organization::all();
        return view('admin.groups.create')
                ->with(compact('grades'))
                ->with(compact('periods'))
                ->with(compact('organizations'));
    }
    
    public function store()
    {
        
        $new_group = $this->validateRequest();
        
        $group = Group::firstOrCreate([
            'title' => $new_group['title'],
            'grade_id' => format_select_input($new_group['grade_id']),
            'period_id' => format_select_input($new_group['period_id']),
            'organization_id' => format_select_input($new_group['organization_id']),
            'owner_id' => auth()->user()->id  
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
        return view('admin.groups.show')
                ->with(compact('group'));
    }
    
    public function edit(Group $group)
    {
        
        abort_unless(\Gate::allows('group_edit'), 403);
        $grades = Grade::all();
        $periods = Period::all();
        $organizations = Organization::all();
        
        return view('admin.groups.edit')
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
            'owner_id' => auth()->user()->id  
        ]);

        return redirect()->route('admin.groups.index');
    }
     /**
     * Remove the specified group from storage.
     *
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
        abort_unless(\Gate::allows('user_enrol'), 403);
        
        
        foreach ((request()->enrollment_list) AS $enrolment)
        {  
            $group = Group::findOrFail((request()->enrollment_list[0]['group_id']));
            $user = User::findOrFail($enrolment['user_id']);
            //if user isn't enrolled to organization, enrol with student role
            OrganizationRoleUser::firstOrCreate([
                                        'user_id'         => $user->id,
                                        'organization_id' => $group->organization->id,
                                        'role_id'         => 6, //enrol as student
                                    ]);
            
            
            $return[] = $user->groups()->syncWithoutDetaching([
                'group_id' => $enrolment['group_id']
            ]);
           
        }
        
        return $return;  
    }
    
    public function expel()
    {
        abort_unless(\Gate::allows('user_enrol'), 403);
        
        foreach ((request()->expel_list) AS $expel)
        {  
            $user = User::find($expel['user_id']);
            $return[] = $user->groups()->detach([
                            'group_id' => $expel['group_id']
                        ]);
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
            'owner_id'   => 'sometimes',
        ]);
    }
}

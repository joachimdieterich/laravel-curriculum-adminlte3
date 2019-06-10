<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Group;
use App\Grade;
use App\Period;
use App\Curriculum;
use App\Organization;
use Illuminate\Http\Request;
use Redirect,Response,DB,Config;
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
        dd($groups);
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
                                    . 'class="btn btn-xs btn-success">'
                                    . '<i class="fa fa-list-alt"></i> Show'
                                    . '</a>';
                    }
                    if (\Gate::allows('group_edit')){
                        $actions .= '<a href="'.route('admin.groups.edit', $groups->id).'" '
                                    . 'class="btn btn-xs btn-primary">'
                                    . '<i class="fa fa-edit"></i> Edit'
                                    . '</a>';
                    }
                    if (\Gate::allows('group_delete')){
                        $actions .= '<button type="button" class="btn btn-xs btn-danger" onclick="destroyUser('.$groups->id.')"><i class="fa fa-trash"></i> Delete</button>';
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
        
        //dd($this->validateRequest());
        $new_group = $this->validateRequest();
        $group = Group::firstOrCreate([
            'title' => $new_group['title'],
            'grade_id' => $new_group['grade_id'][0],
            'period_id' => $new_group['period_id'][0],
            'organization_id' => $new_group['organization_id'][0],
            'owner_id' => auth()->user()->id  
        ]);
        
        // axios call? 
        if (request()->wantsJson()){    
            return ['message' => $group->path()];
        }
        
        return redirect($group->path());
    }
    
    /**
     * Display the specified resource.
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

<?php

namespace App\Http\Controllers;

use App\Plan;
use App\PlanType;
use Illuminate\Http\Request;


class PlanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_unless(\Gate::allows('plan_access'), 403);
        
        return view('plans.index'); 
    }
    
    public function list()
    {
        abort_unless(\Gate::allows('plan_access'), 403);
        $plans = (auth()->user()->role()->id == 1) ? Plan::all() : auth()->user()->plans();
              
        return DataTables::of($plans)
            ->addColumn('action', function ($plans) {
                 $actions  = '';
                    if (\Gate::allows('plan_edit')){
                        $actions .= '<a href="'.route('plan.edit', $plans->id).'"'
                                    . 'id="edit-plan-'.$plans->id.'" '
                                    . 'class="btn p-1">'
                                    . '<i class="fa fa-pencil-alt"></i>' 
                                    . '</a>';
                    }
                    if (\Gate::allows('plan_delete')){
                        $actions .= '<button type="button" '
                                . 'class="btn text-danger" '
                                . 'onclick="destroyDataTableEntry(\'plans\','.$plans->id.')">'
                                . '<i class="fa fa-trash"></i></button>';
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
        abort_unless(\Gate::allows('plan_create'), 403);
        
        $plan  = new Plan();
        $types = PlanType::all();
        
        return view('plans.create')
                ->with(compact('types'))
                ->with(compact('plan'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       abort_unless(\Gate::allows('plan_create'), 403);
        
        $input = $this->validateRequest();
        $plan = Plan::firstOrCreate([
            'title'             => $input['title'],
            'description'       => $input['description'],
            'begin'             => $input['begin'],
            'end'               => $input['end'],
            'duration'          => $input['duration'],
            'type_id'           => format_select_input($input['type_id']),
            'owner_id'          => auth()->user()->id
        ]);
        
         // axios call? 
        if (request()->wantsJson()){    
            return ['message' => $plan->path()];
        }
        
        return redirect($plan->path());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Plan  $plan
     * @return \Illuminate\Http\Response
     */
    public function show(Plan $plan)
    {
        abort_unless(\Gate::allows('plan_show'), 403);
        
        // axios call? 
        if (request()->wantsJson()){  
            return [
                'plan' => $plan
            ];
        }
        
        return view('plans.show')
                ->with(compact('plan'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Plan  $plan
     * @return \Illuminate\Http\Response
     */
    public function edit(Plan $plan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Plan  $plan
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Plan $plan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Plan  $plan
     * @return \Illuminate\Http\Response
     */
    public function destroy(Plan $plan)
    {
        //
    }
    
    protected function validateRequest()
    {               
        
        return request()->validate([
            'title'         => 'sometimes|required',
            'description'   => 'sometimes',
            'begin'         => 'sometimes',
            'end'           => 'sometimes',
            'duration'      => 'sometimes',
            'type_id'       => 'sometimes'
            ]);
    }
}

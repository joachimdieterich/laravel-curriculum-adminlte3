<?php

namespace App\Http\Controllers;

use App\Progress;
use App\Achievement;
use App\EnablingObjective;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProgressController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user_id = 1;
        $terminal_objective  = 2;
        $enabling_objectives = EnablingObjective::where('terminal_objective_id', $terminal_objective)->get();
                
        $total_achieved = Achievement::where('referenceable_type', 'App\\EnablingObjective')
                                ->where('user_id', $user_id)
                                ->whereIn('referenceable_id', $enabling_objectives->pluck('id'))
                                ->where(DB::raw(' RIGHT(status,1) = 1 OR RIGHT(status,1) = 2'))
                                ->get();

        $percentage = $total_achieved->count() / $enabling_objectives->count() *100;         
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        switch ($request->referencable_type) {
            case 'App\TerminalObjective':  
                $input = $this->validateRequest();
               
                $enabling_objectives = \App\EnablingObjective::where('terminal_objective_id', $input['parent_id'])->get();

                $total_achieved = \App\Achievement::where('referenceable_type', 'App\\EnablingObjective')
                                        ->where('user_id', $user_id)
                                        ->whereIn('referenceable_id', $enabling_objectives->pluck('id'))
                                        ->where(DB::raw('RIGHT(status,1) = 1 OR RIGHT(status,1) = 2'))
                                        ->get();
                $progress = Progress::updateOrCreate(
                    [                           
                        'referenceable_type' => $input['referenceable_type'],
                        'referenceable_id' => $input['parent_id'],
                        'associable_type' => 'App\\User',
                        'associable_id' => $user_id
                    ],
                    [
                        'value' => ($total_achieved->count() / $enabling_objectives->count() *100)
                    ]    
                );
                break;

            default:
                break;
        }
       
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Progress  $progress
     * @return \Illuminate\Http\Response
     */
    public function show(Progress $progress)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Progress  $progress
     * @return \Illuminate\Http\Response
     */
    public function edit(Progress $progress)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Progress  $progress
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Progress $progress)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Progress  $progress
     * @return \Illuminate\Http\Response
     */
    public function destroy(Progress $progress)
    {
        //
    }
    
    protected function validateRequest()
    {               
        
        return request()->validate([
            'referenceable_type'    => 'required',
            'referenceable_id'      => 'required',
            'associable_type'       => 'required',
            'associable_id'         => 'required',
            'value'                 => 'required'
            ]);
    }
    
    /**
     * Example (new ProgressController)->calculateProgress('App\TerminalObjective', $terminal_objective_id, $user_id);
     * @param type $parent_model
     * @param type $parent_id
     * @param type $user_id
     * @return type
     */
    public function calculateProgress($parent_model, $parent_id, $user_id)
    {
        $dynamicFunction = 'calculate'.class_basename($parent_model).'Progress';
        return $this->$dynamicFunction($parent_model, $parent_id, $user_id);
    }
    
    /**
     * calculate users terminal objective progress based on enabling objectives achievements 
     * @param type $parent_model
     * @param type $parent_id
     * @param type $user_id
     * @return type
     */
    public function calculateTerminalObjectiveProgress($parent_model, $parent_id, $user_id)
    {
        //Parent App\TerminalObjective
        $enabling_objectives = \App\EnablingObjective::where('terminal_objective_id', $parent_id)->get();

        $total_achieved = Achievement::where('referenceable_type', 'App\\EnablingObjective')
                                ->where('user_id', $user_id)
                                ->whereIn('referenceable_id', $enabling_objectives->pluck('id'))
                                ->whereRaw('(RIGHT(status,1) = "1" OR RIGHT(status,1) = "2")')
                                ->get();
        return Progress::updateOrCreate(
            [
                'referenceable_type' => $parent_model,
                'referenceable_id' => $parent_id,
                'associable_type' => 'App\\User',
                'associable_id' => $user_id,
            ],
            [
                'value' => ($total_achieved->count() / $enabling_objectives->count() *100)    
            ]

        );
    }
}

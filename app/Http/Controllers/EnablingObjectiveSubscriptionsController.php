<?php

namespace App\Http\Controllers;

use App\EnablingObjectiveSubscriptions;
use Illuminate\Http\Request;

class EnablingObjectiveSubscriptionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        $new_subscription= $this->validateRequest();
        $subscription = EnablingObjectiveSubscriptions::firstOrCreate([
            'enabling_objective_id' => $new_subscription['enabling_objective_id'],
            'subscribable_type'     => $new_subscription['subscribable_type'],
            'subscribable_id'       => $new_subscription['subscribable_id'],
            'sharing_level_id'      => 1,
            'visibility'            => true,
            'owner_id'              => auth()->user()->id,	
        ]);
        if (request()->wantsJson()){    
            return ['message' => 'ok'];
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\EnablingObjectiveSubscriptions  $enablingObjectiveSubscriptions
     * @return \Illuminate\Http\Response
     */
    public function show(EnablingObjectiveSubscriptions $enablingObjectiveSubscriptions)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\EnablingObjectiveSubscriptions  $enablingObjectiveSubscriptions
     * @return \Illuminate\Http\Response
     */
    public function edit(EnablingObjectiveSubscriptions $enablingObjectiveSubscriptions)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\EnablingObjectiveSubscriptions  $enablingObjectiveSubscriptions
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EnablingObjectiveSubscriptions $enablingObjectiveSubscriptions)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\EnablingObjectiveSubscriptions  $enablingObjectiveSubscriptions
     * @return \Illuminate\Http\Response
     */
    public function destroy(EnablingObjectiveSubscriptions $enablingObjectiveSubscriptions)
    {
        //
    }
    
    protected function validateRequest()
    {   
        return request()->validate([
            "enabling_objective_id" => 'sometimes|required',
            "subscribable_type" => 'required',
            "subscribable_id" => 'required',
        ]);
    }
}

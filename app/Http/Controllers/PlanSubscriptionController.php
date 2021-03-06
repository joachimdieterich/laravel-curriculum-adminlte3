<?php

namespace App\Http\Controllers;

use App\Plan;
use App\PlanSubscription;
use Illuminate\Http\Request;

class PlanSubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_unless(\Gate::allows('plan_create'), 403);
        if (request()->wantsJson()){
            return [
                'subscribers' =>
                    [
                    'users' =>  auth()->user()->users()->select('users.id','users.firstname','users.lastname')->get(),
                    'groups' => auth()->user()->groups()->select('group_id','title')->get(),
                    'organizations' => auth()->user()->organizations()->select('organization_id','title')->get(),
                    'subscriptions' => Plan::find(request('plan_id'))->subscriptions()->with('subscribable')->get()
                    ]
                ];
        }
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

        $subscribe = PlanSubscription::updateOrCreate([
            "plan_id"           => $input['model_id'],
            "subscribable_type" => $input['subscribable_type'],
            "subscribable_id"   => $input['subscribable_id'],
        ],[
            "editable"=> isset($input['editable']) ? $input['editable'] : false,
            "owner_id"=> auth()->user()->id,
        ]);
        $subscribe->save();

        if (request()->wantsJson()){
            return ['subscription' => Plan::find($input['model_id'])->subscriptions()->with('subscribable')->get()];
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\PlanSubscription  $planSubscription
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PlanSubscription $planSubscription)
    {
        abort_unless(\Gate::allows('plan_edit'), 403);
        $input = $this->validateRequest();

        $planSubscription->update([
            "editable"=> isset($input['editable']) ? $input['editable'] : false,
            "owner_id"=> auth()->user()->id,
        ]);

        if (request()->wantsJson()){
            return ['editable' => $planSubscription->editable];
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\PlanSubscription  $planSubscription
     * @return \Illuminate\Http\Response
     */
    public function destroy(PlanSubscription $planSubscription)
    {
        abort_unless(\Gate::allows('plan_delete'), 403);

        if (request()->wantsJson()){
            return ['message' => $planSubscription->delete()];
        }
    }

    protected function validateRequest()
    {
        return request()->validate([
            'subscribable_type' => 'sometimes|string',
            'subscribable_id'   => 'sometimes|integer',
            'model_id'          => 'sometimes|integer',
            'editable'          => 'sometimes',
        ]);
    }
}

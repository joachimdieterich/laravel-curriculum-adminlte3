<?php

namespace App\Http\Controllers;

use App\TrainingSubscription;
use Illuminate\Http\Request;

class TrainingSubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $input = $this->validateRequest();
        if (isset($input['subscribable_type']) and isset($input['subscribable_id'])) {
            $model = $input['subscribable_type']::find($input['subscribable_id']);
            abort_unless((\Gate::allows('plan_show') and $model->isAccessible()), 403);

            if (request()->wantsJson()) {
                return $model->trainings;
            }
        }
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\TrainingSubscription  $trainingSubscription
     * @return \Illuminate\Http\Response
     */
    public function show(TrainingSubscription $trainingSubscription)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\TrainingSubscription  $trainingSubscription
     * @return \Illuminate\Http\Response
     */
    public function edit(TrainingSubscription $trainingSubscription)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\TrainingSubscription  $trainingSubscription
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TrainingSubscription $trainingSubscription)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\TrainingSubscription  $trainingSubscription
     * @return \Illuminate\Http\Response
     */
    public function destroy(TrainingSubscription $trainingSubscription)
    {
        //
    }

    protected function validateRequest()
    {
        return request()->validate([
            'subscribable_type' => 'sometimes|string',
            'subscribable_id'   => 'sometimes|integer',
        ]);
    }
}

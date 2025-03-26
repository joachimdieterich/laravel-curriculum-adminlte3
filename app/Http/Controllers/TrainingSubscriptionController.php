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
                // INFO: if trainings can be added to other models in the future, its subscriptions need to be filtered for the order_id to work correctly
                return $model->trainings()->orderBy('order_id')->get();
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

    public function higher(TrainingSubscription $trainingSubscription)
    {
        // decrease order_id of the training with the next highest order_id
        TrainingSubscription::where([
            'subscribable_type' => $trainingSubscription->subscribable_type,
            'subscribable_id' => $trainingSubscription->subscribable_id,
            'order_id' => $trainingSubscription->order_id + 1,
        ])->decrement('order_id', 1);

        $trainingSubscription->order_id++;
        $trainingSubscription->save();


        $model = $trainingSubscription->subscribable_type::find($trainingSubscription->subscribable_id);
        return $model->trainings()->orderBy('order_id')->get();
    }

    public function lower(TrainingSubscription $trainingSubscription)
    {
        // increase order_id of the training with the next lowest order_id
        TrainingSubscription::where([
            'subscribable_type' => $trainingSubscription->subscribable_type,
            'subscribable_id' => $trainingSubscription->subscribable_id,
            'order_id' => $trainingSubscription->order_id - 1,
        ])->increment('order_id', 1);

        $trainingSubscription->order_id--;
        $trainingSubscription->save();

        $model = $trainingSubscription->subscribable_type::find($trainingSubscription->subscribable_id);
        return $model->trainings()->orderBy('order_id')->get();
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

<?php

namespace App\Http\Controllers;

use App\TaskSubscription;
use Illuminate\Http\Request;

class TaskSubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_unless(\Gate::allows('task_access'), 403);
        $input = $this->validateRequest();
        if (isset($input['subscribable_type']) AND isset($input['subscribable_id']))
        {
            $subscriptions = TaskSubscription::where([
                'subscribable_type' => $input['subscribable_type'],
                'subscribable_id'   => $input['subscribable_id']
            ]);

            if (request()->wantsJson()){

                return ['subscriptions' => $subscriptions->with(['task'])->get()];
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
     * @param  \App\TaskSubscription  $taskSubscription
     * @return \Illuminate\Http\Response
     */
    public function show(TaskSubscription $taskSubscription)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\TaskSubscription  $taskSubscription
     * @return \Illuminate\Http\Response
     */
    public function edit(TaskSubscription $taskSubscription)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\TaskSubscription  $taskSubscription
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TaskSubscription $taskSubscription)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\TaskSubscription  $taskSubscription
     * @return \Illuminate\Http\Response
     */
    public function destroy(TaskSubscription $taskSubscription)
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

<?php

namespace App\Http\Controllers;

use App\Plugins\Lms\LmsPlugin;
use Illuminate\Http\Request;

class LmsSubscriptionController extends Controller
{
    /**
     * Get data over ws
     *
     * @return \Illuminate\Http\Response
     */
    public function get(Request $request)
    {
        $input = $this->validateRequest();
        $lms = new LmsPlugin();

        if (request()->wantsJson()) {
            return ['message' => $lms->plugins[env('LMSPLUGIN')]->{$input['ws_function']}($input)];
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $input = $this->validateRequest();
        $lms = new LmsPlugin();

        if (request()->wantsJson()) {
            return ['message' => $lms->plugins[env('LMSPLUGIN')]->$input['ws_function']($input)];
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
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $this->validateRequest();

        $repositoryPlugin = app()->make('App\Plugins\Lms\LmsPlugin');
        if (request()->wantsJson()) {
            return ['subscription' => $repositoryPlugin->plugins[$input['plugin']]->store($request)];
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\LmsSubscription $lmsSubscription
     * @return \Illuminate\Http\Response
     */
    public function show(LmsSubscription $lmsSubscription)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\LmsSubscription $lmsSubscription
     * @return \Illuminate\Http\Response
     */
    public function edit(LmsSubscription $lmsSubscription)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\LmsSubscription $lmsSubscription
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, LmsSubscription $lmsSubscription)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\LmsSubscription $lmsSubscription
     * @return \Illuminate\Http\Response
     */
    public function destroy(LmsSubscription $lmsSubscription)
    {
        //
    }

    protected function validateRequest()
    {

        return request()->validate([
            'plugin' => 'required',
            'course_id' => 'sometimes',
            'course_content_id' => 'sometimes',
            'course_item' => 'sometimes',
            'ws_function' => 'sometimes|required',
            'subscribable_type' => 'sometimes|required',
            'subscribable_id' => 'sometimes|required',
        ]);
    }
}

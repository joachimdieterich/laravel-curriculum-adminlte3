<?php

namespace App\Http\Controllers;

use App\Plugins\Lms\LmsPlugin;
use App\LmsReferenceSubscription;
use Illuminate\Http\Request;

class LmsReferenceController extends Controller
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
     * @param \App\LmsReferenceSubscription $lmsSubscription
     * @return \Illuminate\Http\Response
     */
    public function show(LmsReferenceSubscription $lmsReferenceSubscription)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\LmsReferenceSubscription $lmsReferenceSubscription
     * @return \Illuminate\Http\Response
     */
    public function edit(LmsReferenceSubscription $lmsReferenceSubscription)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\LmsReferenceSubscription $lmsSubscription
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, LmsReferenceSubscription $lmsReferenceSubscription)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\LmsReferenceSubscription $lmsReferenceSubscription
     * @return \Illuminate\Http\Response
     */
    public function destroy(LmsReferenceSubscription $lmsReferenceSubscription)
    {
        abort_unless(\Gate::allows('lms_delete'), 403);

        $lmsreferenceSubscription->delete();
    }

    protected function validateRequest()
    {

        return request()->validate([
            'plugin' => 'required',
            'id' => 'sometimes',
            'course_id' => 'sometimes',
            'course_content_id' => 'sometimes',
            'course_item' => 'sometimes',
            'ws_function' => 'sometimes|required',
            'referenceable_type' => 'sometimes|required',
            'referenceable_id' => 'sometimes|required',
            'sharing_level' => 'sometimes|required',
        ]);
    }
}

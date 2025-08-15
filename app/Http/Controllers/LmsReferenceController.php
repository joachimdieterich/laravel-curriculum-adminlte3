<?php

namespace App\Http\Controllers;

use App\LmsReference;
use App\Organization;
use App\Plugins\Lms\LmsPlugin;
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
            return [
                'entries' => $lms->plugins[env('LMSPLUGIN')]->{$input['ws_function']}($input),
                'lms_url' => Organization::find(auth()->user()->current_organization_id)->lms_url
            ];
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $this->validateRequest();

        $lms = new LmsPlugin();
        if (request()->wantsJson()) {
            return $lms->plugins[$input['plugin']]->store($input);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\LmsReference  $lmsReference
     * @return \Illuminate\Http\Response
     */
    public function show(LmsReference $lmsReference)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\LmsReference  $lmsReference
     * @return \Illuminate\Http\Response
     */
    public function edit(LmsReference $lmsReference)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\LmsReference  $lmsReference
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, LmsReference $lmsReference)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\LmsReference  $lmsReference
     * @return \Illuminate\Http\Response
     */
    public function destroy(LmsReference $lmsReference)
    {
        abort_unless(\Gate::allows('lms_delete') and ($lmsReference->owner_id == auth()->id()), 403);

        if (request()->wantsJson()) {
            $lmsReference->subscriptions()->delete();

            return ['message' => $lmsReference->delete()];
        }
    }

    protected function validateRequest()
    {
        return request()->validate([
            'plugin' => 'required',
            'id' => 'sometimes',
            'course_id' => 'sometimes',
            'course_content_id' => 'sometimes',
            'course_item' => 'sometimes',
            'ws_function' => 'sometimes',
            'referenceable_type' => 'sometimes',
            'referenceable_id' => 'sometimes',
            'sharing_level' => 'sometimes',
        ]);
    }
}

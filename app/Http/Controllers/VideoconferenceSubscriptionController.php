<?php

namespace App\Http\Controllers;

use App\Videoconference;
use App\VideoconferenceSubscription;
use Illuminate\Http\Request;

class VideoconferenceSubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $videoconference = Videoconference::find(request('videoconference_id'));
        abort_unless((\Gate::allows('videoconference_access') and $videoconference->isAccessible()), 403);

        if (request()->wantsJson()) {
            return [
                'subscribers' => [
                    'subscriptions' => $videoconference->subscriptions()->with('subscribable')->get(),
                ],
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
        $input = $this->validateRequest();
        $videoconference = Videoconference::find($input['model_id']);
        abort_unless((\Gate::allows('videoconference_create') and $videoconference->isAccessible()), 403);

        $subscribe = VideoconferenceSubscription::updateOrCreate([
            'videoconference_id' => $input['model_id'],
            'subscribable_type' => $input['subscribable_type'],
            'subscribable_id' => $input['subscribable_id'],
        ], [
            'editable' => isset($input['editable']) ? $input['editable'] : false,
            'owner_id' => auth()->user()->id,
        ]);
        $subscribe->save();

        if (request()->wantsJson()) {
            return ['subscription' => $videoconference->subscriptions()->with('subscribable')->get()];
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\VideoconferenceSubscription  $videoconferenceSubscription
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, VideoconferenceSubscription $videoconferenceSubscription)
    {
        abort_unless((\Gate::allows('videoconference_edit') and $videoconferenceSubscription->isAccessible()), 403);
        $input = $this->validateRequest();

        $videoconferenceSubscription->update([
            'editable'=> isset($input['editable']) ? $input['editable'] : false,
            'owner_id'=> auth()->user()->id,
        ]);

        if (request()->wantsJson()) {
            return ['editable' => $videoconferenceSubscription->editable];
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\VideoconferenceSubscription  $videoconferenceSubscription
     * @return \Illuminate\Http\Response
     */
    public function destroy(VideoconferenceSubscription $videoconferenceSubscription)
    {
        abort_unless((\Gate::allows('videoconference_delete') and $videoconferenceSubscription->isAccessible()), 403);

        if (request()->wantsJson()) {
            return ['message' => $videoconferenceSubscription->delete()];
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

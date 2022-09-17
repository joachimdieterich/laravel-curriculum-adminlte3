<?php

namespace App\Http\Controllers;

use App\LmsReference;
use App\LmsReferenceSubscription;
use Illuminate\Http\Request;

class LmsReferenceSubscriptionController extends Controller
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
            $subscriptions = LmsReferenceSubscription::where([
                'subscribable_type' => $input['subscribable_type'],
                'subscribable_id' => $input['subscribable_id'],
            ]);

            if (request()->wantsJson()) {
                return ['subscriptions' => $subscriptions->with(['lms_reference'])->get()];
            }
        } else {
            if (request()->wantsJson()) {
                return [
                    'subscribers' => [
                        'subscriptions' => optional(
                                optional(
                                    LmsReference::find(request('lmsReference_id'))
                                )->subscriptions()
                            )->with('subscribable')->get(),
                    ],
                ];
            }
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
        abort_unless(\Gate::allows('lms_create'), 403);
        $input = $this->validateRequest();

        $subscribe = LmsReferenceSubscription::updateOrCreate([
            'lms_reference_id' => $input['model_id'],
            'subscribable_type' => $input['subscribable_type'],
            'subscribable_id' => $input['subscribable_id'],
        ], [
            'editable' => isset($input['editable']) ? $input['editable'] : false,
            'owner_id' => auth()->user()->id,
        ]);
        $subscribe->save();

        if (request()->wantsJson()) {
            return ['subscription' => LmsReference::find($input['model_id'])->subscriptions()->with('subscribable')->get()];
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\LmsReferenceSubscription  $LmsReferenceSubscription
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, LmsReferenceSubscription $LmsReferenceSubscription)
    {
        abort_unless(\Gate::allows('lms_edit'), 403);
        $input = $this->validateRequest();

        $LmsReferenceSubscription->update([
            'editable' => isset($input['editable']) ? $input['editable'] : false,
            'owner_id' => auth()->user()->id,
        ]);

        if (request()->wantsJson()) {
            return ['editable' => $LmsReferenceSubscription->editable];
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\LmsReferenceSubscription  $lmsReferenceSubscription
     * @return \Illuminate\Http\Response
     */
    public function destroy(LmsReferenceSubscription $lmsReferenceSubscription)
    {
        abort_unless(\Gate::allows('lms_delete'), 403);

        if (request()->wantsJson()) {
            return ['message' => $lmsReferenceSubscription->delete()];
        }
    }

    protected function validateRequest()
    {
        return request()->validate([
            'subscribable_type' => 'sometimes|string',
            'subscribable_id' => 'sometimes|integer',
            'model_id' => 'sometimes|integer',
            'editable' => 'sometimes',
        ]);
    }
}

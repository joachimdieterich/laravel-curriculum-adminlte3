<?php

namespace App\Http\Controllers;

use App\Logbook;
use App\LogbookSubscription;
use Illuminate\Http\Request;

class LogbookSubscriptionController extends Controller
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
            abort_unless((\Gate::allows('logbook_access') and $model->isAccessible()), 403);

            $subscriptions = LogbookSubscription::where([
                'subscribable_type' => $input['subscribable_type'],
                'subscribable_id' => $input['subscribable_id'],
            ]);

            if (request()->wantsJson()) {
                return ['subscriptions' => $subscriptions->with(['logbook'])->get()];
            }
        } else {
            if (request()->wantsJson()) {
                return [
                    'subscribers' => [
                        'subscriptions' => optional(
                                optional(
                                    Logbook::find(request('logbook_id'))
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
        $input = $this->validateRequest();
        abort_unless((\Gate::allows('logbook_create') and Logbook::find($input['model_id'])->isAccessible()), 403);

        $subscribe = LogbookSubscription::updateOrCreate([
            'logbook_id' => $input['model_id'],
            'subscribable_type' => $input['subscribable_type'],
            'subscribable_id' => $input['subscribable_id'],
        ], [
            'editable' => isset($input['editable']) ? $input['editable'] : false,
            'owner_id' => auth()->user()->id,
        ]);
        $subscribe->save();

        if (request()->wantsJson()) {
            return ['subscription' => Logbook::find($input['model_id'])->subscriptions()->with('subscribable')->get()];
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\LogbookSubscription  $logbookSubscription
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, LogbookSubscription $logbookSubscription)
    {
        $input = $this->validateRequest();
        abort_unless((\Gate::allows('logbook_edit') and $logbookSubscription->isAccessible()), 403);

        $logbookSubscription->update([
            'editable' => isset($input['editable']) ? $input['editable'] : false,
            'owner_id' => auth()->user()->id,
        ]);

        if (request()->wantsJson()) {
            return ['editable' => $logbookSubscription->editable];
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\LogbookSubscription  $logbookSubscription
     * @return \Illuminate\Http\Response
     */
    public function destroy(LogbookSubscription $logbookSubscription)
    {
        abort_unless((\Gate::allows('logbook_delete') and $logbookSubscription->isAccessible()), 403);
        if (request()->wantsJson()) {
            return ['message' => $logbookSubscription->delete()];
        } else {
            $logbookSubscription->delete();
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

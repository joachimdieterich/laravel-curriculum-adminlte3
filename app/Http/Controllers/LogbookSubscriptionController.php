<?php

namespace App\Http\Controllers;

use App\Logbook;
use App\LogbookSubscription;
use App\Helpers\QRCodeHelper;
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
        if (request()->wantsJson()) {
            return [
                'subscriptions' => optional(
                        optional(
                            Logbook::find(request('logbook_id'))
                        )->subscriptions()
                    )->with('subscribable')
                    ->whereHasMorph('subscribable', '*', function ($q, $type) {
                        if ($type == 'App\\User') {
                            $q->whereNot('id', config('app.guest_user_id'));
                        }
                    })->get(),
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
        $logbook = Logbook::select('id')->find($input['logbook_id']);
        abort_unless(\Gate::allows('logbook_create') && $logbook->isAccessible(), 403);

        $subscribe = LogbookSubscription::updateOrCreate([
            'logbook_id'        => $logbook->id,
            'subscribable_type' => $input['subscribable_type'],
            'subscribable_id'   => $input['subscribable_id'],
        ], [
            'editable' => isset($input['editable']) ? $input['editable'] : false,
            'owner_id' => auth()->user()->id,
        ]);
        $subscribe->save();

        return $subscribe->with('logbook')->find($subscribe->id);
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function expel(Request $request)
    {
        $input = $this->validateRequest();
        $logbook = Logbook::select('id')->find($input['logbook_id']);
        abort_unless(\Gate::allows('logbook_create') && $logbook->isAccessible(), 403);

        LogbookSubscription::where([
            'logbook_id'        => $logbook->id,
            'subscribable_type' => $input['subscribable_type'],
            'subscribable_id'   => $input['subscribable_id'],
        ])->delete();
    }

    protected function validateRequest()
    {
        return request()->validate([
            'subscribable_type' => 'sometimes|string',
            'subscribable_id'   => 'sometimes|integer',
            'logbook_id'        => 'sometimes|integer',
            'editable'          => 'sometimes',
        ]);
    }
}

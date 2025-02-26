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
        // $tokens = null;
        if (request()->wantsJson())
        {
            // $tokenscodes = LogbookSubscription::where('logbook_id', request('logbook_id'))
            //     ->where('sharing_token', "!=", null)
            //     ->get();

            // foreach ($tokenscodes as $token)
            // {
            //     $tokens[] = [
            //         "token" => $token,
            //         "qr"    => (new QRCodeHelper())
            //             ->generateQRCodeByString(
            //                 env("APP_URL"). "/logbooks/" . request('logbook_id') ."/token?sharing_token=" .$token->sharing_token
            //             )
            //     ];
            // }

            return [
                // 'tokens' => $tokens,
                'subscriptions' => optional(
                        optional(
                            Logbook::find(request('logbook_id'))
                        )->subscriptions()
                    )->with('subscribable')
                    ->whereHasMorph('subscribable', '*', function ($q, $type) {
                        if ($type == 'App\\User') {
                            $q->whereNot('id', env('GUEST_USER'));
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
        $logbook = Logbook::find(format_select_input($input['model_id']));
        abort_unless((\Gate::allows('logbook_create') and $logbook->isAccessible()), 403);

        $subscribe = LogbookSubscription::updateOrCreate([
            'logbook_id' => $logbook->id,
            'subscribable_type' => $input['subscribable_type'],
            'subscribable_id' => $input['subscribable_id'],
        ], [
            'editable' => isset($input['editable']) ? $input['editable'] : false,
            'owner_id' => auth()->user()->id,
        ]);
        $subscribe->save();

        if (request()->wantsJson()) {
            return $subscribe->with(['subscribable', 'logbook'])->find($subscribe->id);
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function expel(Request $request)
    {
        $input = $this->validateRequest();
        $logbook = Logbook::find(format_select_input($input['model_id']));
        abort_unless((\Gate::allows('logbook_create') and $logbook->isAccessible()), 403);

        $subscription = LogbookSubscription::where([
            'logbook_id' => $logbook->id,
            'subscribable_type' => $input['subscribable_type'],
            'subscribable_id' => $input['subscribable_id'],
        ]);

        if ($subscription->delete()) {
            return trans('global.expel_success');
        }
    }

    protected function validateRequest()
    {
        return request()->validate([
            'subscribable_type' => 'sometimes|string',
            'subscribable_id' => 'sometimes|integer',
            'model_id' => 'sometimes', //array or int
            'editable' => 'sometimes',
        ]);
    }
}

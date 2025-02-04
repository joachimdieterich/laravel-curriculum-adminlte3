<?php

namespace App\Http\Controllers;

use App\Helpers\QRCodeHelper;
use App\Videoconference;
use App\VideoconferenceSubscription;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use function Laravel\Prompts\form;

class VideoconferenceSubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tokens = null;
        if (request()->wantsJson())
        {
            $tokenscodes = VideoconferenceSubscription::where('videoconference_id', request('videoconference_id'))
                ->where('sharing_token', "!=", null)
                ->get();

            foreach ($tokenscodes as $token)
            {
                $tokens[] = [
                    "token" => $token,
                    "qr"    => (new QRCodeHelper())
                        ->generateQRCodeByString(
                            env("APP_URL"). "/videoconferences/" . request('videoconference_id') ."/token?sharing_token=" .$token->sharing_token
                        )
                ];
            }
            return [
                'tokens' => $tokens ?? [],
                'subscriptions' => optional(
                        optional(
                            Videoconference::find(request('videoconference_id'))
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
        $videoconference = Videoconference::find(format_select_input($input['model_id']));
        abort_unless((\Gate::allows('videoconference_create') and $videoconference->isAccessible()), 403);

        $subscribe = VideoconferenceSubscription::updateOrCreate([
            'videoconference_id' => $videoconference->id,
            'subscribable_type' => $input['subscribable_type'],
            'subscribable_id' => $input['subscribable_id'],
        ], [
            'editable' => isset($input['editable']) ? $input['editable'] : false,
            'owner_id' => auth()->user()->id,
        ]);
        $subscribe->save();

        if (request()->wantsJson()) {
            return $subscribe->with(['subscribable', 'videoconference'])->find($subscribe->id);
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

    public function expel(Request $request) {
        $input = $this->validateRequest();
        $vc = Videoconference::find(format_select_input($input['model_id']));
        abort_unless((\Gate::allows('videoconference_delete') and $vc->isAccessible()), 403);

        $subscription = VideoconferenceSubscription::where([
            'videoconference_id' => $vc->id,
            'subscribable_id' => $input['subscribable_id'],
            'subscribable_type' => $input['subscribable_type'],
        ]);

        if ($subscription->delete()) {
            return trans('global.expel_success');
        }
    }

    protected function validateRequest()
    {
        return request()->validate([
            'subscribable_type' => 'sometimes|string',
            'subscribable_id'   => 'sometimes|integer',
            'model_id'          => 'sometimes',
            'editable'          => 'sometimes',
            'videoconference_id'=> 'sometimes',
        ]);
    }
}

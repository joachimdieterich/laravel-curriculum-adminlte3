<?php

namespace App\Http\Controllers;

use App\Helpers\QRCodeHelper;
use App\MapSubscription;
use App\Map;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class MapSubscriptionController extends Controller
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
            abort_unless((\Gate::allows('map_access') and $model->isAccessible()), 403);

            $map = $model->maps;

            return empty($map) ? '' : DataTables::of($map)
                ->setRowId('id')
                ->make(true);
        }
        else
        {
            if (request()->wantsJson())
            {

                $tokenscodes = MapSubscription::where('map_id', request('map_id'))
                    ->where('sharing_token', "!=", null)
                    ->get();

                foreach ($tokenscodes as $token)
                {
                    $tokens[] = [
                        "token" => $token,
                        "qr"    => (new QRCodeHelper())
                            ->generateQRCodeByString(
                                env("APP_URL"). "/maps/" . request('map_id') ."/token?sharing_token=" .$token->sharing_token
                            )
                    ];
                }
                return [
                    'subscribers' => [
                        'tokens' => $tokens ?? [],
                        'subscriptions' => optional(
                            optional(
                                Map::find(request('map_id'))
                            )->subscriptions()
                        )->with('subscribable')
                            ->whereHasMorph('subscribable', '*', function ($q, $type) {
                                if ($type == 'App\\User') {
                                    $q->whereNot('id', env('GUEST_USER'));
                                }
                            })->get(),
                        //'subscriptions' => $map->subscriptions()->with('subscribable')->get(),
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
        $map = Map::find($input['model_id']);
        abort_unless((\Gate::allows('map_create') and $map->isAccessible()), 403);

        $subscribe = MapSubscription::updateOrCreate([
            'map_id' => $input['model_id'],
            'subscribable_type' => $input['subscribable_type'],
            'subscribable_id' => $input['subscribable_id'],
        ], [
            'editable' => isset($input['editable']) ? $input['editable'] : false,
            'owner_id' => auth()->user()->id,
        ]);
        $subscribe->save();

        if (request()->wantsJson()) {
            return ['subscription' => $map->subscriptions()->with('subscribable')->get()];
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\MapSubscription  $mapSubscription
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MapSubscription $mapSubscription)
    {
        abort_unless((\Gate::allows('map_edit') and $mapSubscription->isAccessible()), 403);
        $input = $this->validateRequest();

        $mapSubscription->update([
            'editable'=> isset($input['editable']) ? $input['editable'] : false,
            'owner_id'=> auth()->user()->id,
        ]);

        if (request()->wantsJson()) {
            return ['editable' => $mapSubscription->editable];
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\MapSubscription  $mapSubscription
     * @return \Illuminate\Http\Response
     */
    public function destroy(MapSubscription $mapSubscription)
    {
        abort_unless((\Gate::allows('map_delete') and $mapSubscription->isAccessible()), 403);

        if (request()->wantsJson()) {
            return ['message' => $mapSubscription->delete()];
        }
    }

    protected function validateRequest()
    {
        return request()->validate([
            'subscribable_type' => 'sometimes|string',
            'subscribable_id'   => 'sometimes|integer',
            'model_id'          => 'sometimes|integer',
            'editable'          => 'sometimes',
            'map_id'            => 'sometimes',
        ]);
    }
}

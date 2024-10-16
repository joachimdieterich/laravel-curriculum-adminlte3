<?php

namespace App\Http\Controllers;

use App\MapMarker;
use App\MapMarkerSubscription;
use Gate;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Helpers\QRCodeHelper;

class MapMarkerSubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     * @throws \Exception
     */
    public function index()
    {
        $input = $this->validateRequest();
        if (isset($input['subscribable_type']) and isset($input['subscribable_id'])) {
            $model = $input['subscribable_type']::find($input['subscribable_id']);
            abort_unless((Gate::allows('map_access') and $model->isAccessible()), 403);

            $mapMarker = $model->mapMarker;

            return empty($mapMarker) ? '' : DataTables::of($mapMarker)
                ->setRowId('id')
                ->make(true);
        }
        else
        {
            if (request()->wantsJson())
            {

                $tokenCodes = MapMarkerSubscription::where('map_marker_id', request('map_marker_id'))
                    ->where('sharing_token', "!=", null)
                    ->get();

                foreach ($tokenCodes as $token)
                {
                    $tokens[] = [
                        "token" => $token,
                        "qr"    => (new QRCodeHelper())
                            ->generateQRCodeByString(
                                env("APP_URL"). "/mapMarkers/" . request('map_marker_id') ."/token?sharing_token=" .$token->sharing_token
                            )
                    ];
                }
                return [
                    'subscribers' => [
                        'tokens' => $tokens ?? [],
                        'subscriptions' => optional(
                            optional(
                                MapMarker::find(request('map_marker_id'))
                            )->subscriptions()
                        )->with('subscribable')
                            ->whereHasMorph('subscribable', '*', function ($q, $type) {
                                if ($type == 'App\\User') {
                                    $q->whereNot('id', env('GUEST_USER'));
                                }
                            })->get(),
                    ],
                ];
            }
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        abort(403);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $input = $this->validateRequest();
        $mapMarker = MapMarker::find($input['model_id']);
        abort_unless((Gate::allows('map_create') and $mapMarker->isAccessible()), 403);

        $subscribe = MapMarkerSubscription::updateOrCreate([
            'map_marker_id' => $input['model_id'],
            'subscribable_type' => $input['subscribable_type'],
            'subscribable_id' => $input['subscribable_id'],
        ], [
            'editable' => $input['editable'] ?? false,
            'owner_id' => auth()->user()->id,
        ]);
        $subscribe->save();

        if (request()->wantsJson()) {
            return ['subscription' => $mapMarker->subscriptions()->with('subscribable')->get()];
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(MapMarkerSubscription $mapMarkerSubscription)
    {
       abort(403);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MapMarkerSubscription $mapMarkerSubscription)
    {
        abort(403);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MapMarkerSubscription $mapMarkerSubscription)
    {
        abort_unless((Gate::allows('map_edit') and $mapMarkerSubscription->isAccessible()), 403);
        $input = $this->validateRequest();

        $mapMarkerSubscription->update([
            'editable'=> $input['editable'] ?? false,
            'owner_id'=> auth()->user()->id,
        ]);

        if (request()->wantsJson()) {
            return ['editable' => $mapMarkerSubscription->editable];
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MapMarkerSubscription $mapMarkerSubscription)
    {
        abort_unless((Gate::allows('map_delete') and $mapMarkerSubscription->isAccessible()), 403);

        if (request()->wantsJson()) {
            return $mapMarkerSubscription->delete();
        }
    }

    protected function validateRequest()
    {
        return request()->validate([
            'subscribable_type' => 'sometimes|string',
            'subscribable_id'   => 'sometimes|integer',
            'model_id'          => 'sometimes|integer',
            'editable'          => 'sometimes',
            'map_marker_id'     => 'sometimes',
        ]);
    }
}

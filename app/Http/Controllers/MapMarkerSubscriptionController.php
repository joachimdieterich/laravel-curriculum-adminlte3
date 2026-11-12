<?php

namespace App\Http\Controllers;

use App\Helpers\QRCodeHelper;
use App\MapMarker;
use App\MapMarkerSubscription;
use Carbon\Carbon;
use Exception;
use Gate;
use Yajra\DataTables\DataTables;

class MapMarkerSubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return array|string|void
     *
     * @throws Exception
     */
    public function index()
    {
        $input = $this->validateRequest();
        if (isset($input['subscribable_type'], $input['subscribable_id'])) {
            $model = $input['subscribable_type']::find($input['subscribable_id']);
            abort_unless((Gate::allows('map_access') and $model->isAccessible()), 403);

            $mapMarker = $model->mapMarker;

            return empty($mapMarker) ? '' : DataTables::of($mapMarker)
                ->setRowId('id')
                ->make();
        }

        if (request()->wantsJson()) {

            $tokenCodes = MapMarkerSubscription::where('map_marker_id', request('map_marker_id'))
                ->where('sharing_token', '!=', null)
                ->get();

            foreach ($tokenCodes as $token) {
                $tokens[] = [
                    'token' => $token,
                    'qr'    => (new QRCodeHelper)
                        ->generateQRCodeByString(
                            config('app.url') . '/mapMarkers/' . request('map_marker_id') . '/token?sharing_token=' . $token->sharing_token
                        ),
                ];
            }

            return [
                'subscribers' => [
                    'tokens'        => $tokens ?? [],
                    'subscriptions' => MapMarker::find(request('map_marker_id'))
                        ?->subscriptions()
                        ?->with('subscribable')
                        ->whereHasMorph('subscribable', '*', function ($q, $type) {
                            if ($type == 'App\\User') {
                                $q->whereNot('id', config('app.guest_user_id'));
                            }
                        })->get(),
                ],
            ];
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return array|void
     */
    public function store()
    {
        $input     = $this->validateRequest();
        $mapMarker = MapMarker::find($input['model_id']);
        abort_unless((Gate::allows('map_create') and $mapMarker->isAccessible()), 403);

        $subscribe = MapMarkerSubscription::updateOrCreate([
            'map_marker_id'     => $input['model_id'],
            'subscribable_type' => $input['subscribable_type'],
            'subscribable_id'   => $input['subscribable_id'],
            'due_date'          => is_null($input['expire_date']) ? $input['expire_date'] : Carbon::createFromDate($input['expire_date'])->toDateString(),
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
     * Update the specified resource in storage.
     *
     * @return array|void
     */
    public function update(MapMarkerSubscription $mapMarkerSubscription)
    {
        abort_unless((Gate::allows('map_edit') and $mapMarkerSubscription->isAccessible()), 403);
        $input = $this->validateRequest();

        $mapMarkerSubscription->update([
            'editable' => $input['editable'] ?? false,
            'owner_id' => auth()->user()->id,
        ]);

        if (request()->wantsJson()) {
            return ['editable' => $mapMarkerSubscription->editable];
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return bool|void
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
            'expire_date'       => 'sometimes|nullable|date',
        ]);
    }
}

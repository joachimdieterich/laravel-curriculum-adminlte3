<?php

namespace App\Http\Controllers;

use App\Helpers\QRCodeHelper;
use App\Map;
use App\MapSubscription;
use Carbon\Carbon;
use Exception;
use Gate;
use Yajra\DataTables\DataTables;

class MapSubscriptionController extends Controller
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

            $map = $model->maps;

            return empty($map) ? '' : DataTables::of($map)
                ->setRowId('id')
                ->make();
        }

        if (request()->wantsJson()) {

            $tokenCodes = MapSubscription::where('map_id', request('map_id'))
                ->where('sharing_token', '!=', null)
                ->get();

            foreach ($tokenCodes as $token) {
                $tokens[] = [
                    'token' => $token,
                    'qr'    => (new QRCodeHelper)
                        ->generateQRCodeByString(
                            config('app.url') . '/maps/' . request('map_id') . '/token?sharing_token=' . $token->sharing_token
                        ),
                ];
            }

            return [
                'tokens'        => $tokens ?? [],
                'subscriptions' => Map::find(request('map_id'))
                    ?->subscriptions()
                    ?->with('subscribable')
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
     * @return MapSubscription|void
     */
    public function store()
    {
        $input = $this->validateRequest();
        $map   = Map::find($input['model_id']);
        abort_unless((Gate::allows('map_create') and $map->isAccessible()), 403);

        $subscribe = MapSubscription::updateOrCreate([
            'map_id'            => $input['model_id'],
            'subscribable_type' => $input['subscribable_type'],
            'subscribable_id'   => $input['subscribable_id'],
            'due_date'          => is_null($input['expire_date']) ? $input['expire_date'] : Carbon::createFromDate($input['expire_date'])->toDateString(),
        ], [
            'editable' => $input['editable'] ?? false,
            'owner_id' => auth()->user()->id,
        ]);
        $subscribe->save();

        if (request()->wantsJson()) {
            return $subscribe->with('subscribable')->find($subscribe->id);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @return array|void
     */
    public function update(MapSubscription $mapSubscription)
    {
        abort_unless((Gate::allows('map_edit') and $mapSubscription->isAccessible()), 403);
        $input = $this->validateRequest();

        $mapSubscription->update([
            'editable' => $input['editable'] ?? false,
            'owner_id' => auth()->user()->id,
        ]);

        if (request()->wantsJson()) {
            return ['editable' => $mapSubscription->editable];
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return array|void
     */
    public function destroy(MapSubscription $mapSubscription)
    {
        abort_unless((Gate::allows('map_delete') and $mapSubscription->isAccessible()), 403);

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
            'expire_date'       => 'sometimes|nullable|date',
        ]);
    }
}

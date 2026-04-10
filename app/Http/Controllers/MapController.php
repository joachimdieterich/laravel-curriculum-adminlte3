<?php

namespace App\Http\Controllers;

use App\Map;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\MapSubscription;

class MapController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_unless(\Gate::allows('map_access'), 403);

        return view('map.index');
    }

    public function list(Request $request): \Illuminate\Http\JsonResponse
    {
        abort_unless(\Gate::allows('map_access'), 403);

        $maps = Map::without('markers');

        return getDataTableWithEntries($maps);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        abort_unless(\Gate::allows('map_create'), 403);

        $input = $this->validateRequest();

        $map = Map::create([
            'title' => $input['title'],
            'subtitle'=> $input['subtitle'],
            'description'=> $input['description'],
            'tags'=> $input['tags'],
            'type_id'=> $input['type_id'],
            'category_id'=> $input['category_id'],
            'border_url'=> $input['border_url'] ?? "https://nominatim.openstreetmap.org/search?polygon_geojson=1&format=geojson&polygon_threshold=0.005&country=de&state=RP",
            'latitude'=> $input['latitude'] ?? 49,
            'longitude'=> $input['longitude'] ?? 8,
            'zoom'=> $input['zoom'] ?? 10,
            'color'=> $input['color'] ?? '#F2C511',
            'owner_id' => auth()->user()->id,
        ]);

        if (request()->wantsJson()) {
            return $map;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Map  $map
     * @return \Illuminate\Http\Response
     */
    public function show(Map $map, $token = null)
    {
        abort_if(auth()->user()->id == config('app.guest_user_id') && $token == null, 403);

        $editable = $map->isEditable(auth()->user()->id, $token);

        return view('map.show')
            ->with(compact('map', 'editable'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Map  $map
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Map $map)
    {
        abort_unless((\Gate::allows('map_edit') and $map->isAccessible()), 403);
        $input = $this->validateRequest();

        $map->update([
            'title'         => $input['title'] ?? $map->title,
            'subtitle'      => $input['subtitle'],
            'description'   => $input['description'],
            'tags'          => $input['tags'],
            'type_id'       => $input['type_id'] ?? $map->type_id,
            'category_id'   => $input['category_id'] ?? $map->category_id,
            'border_url'    => $input['border_url'] ?? $map->border_url,
            'latitude'      => $input['latitude'] ?? $map->latitude,
            'longitude'     => $input['longitude'] ?? $map->longitude,
            'zoom'          => $input['zoom'] ?? $map->zoom,
            'color'         => $input['color'] ?? $map->color,
            'medium_id'     => $input['medium_id'],
            'owner_id'      => is_admin() ? $input['owner_id'] : auth()->user()->id,
        ]);

        return $map;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Map  $map
     * @return \Illuminate\Http\Response
     */
    public function destroy(Map $map)
    {
        abort_unless((\Gate::allows('map_delete') and $map->isAccessible()), 403);

        $map->delete();
    }

    public function getMapByToken(Map $map, Request $request)
    {
        $input = $this->validateRequest();

        $subscription = MapSubscription::where('sharing_token', $input['sharing_token'] )->get()->first();

        if (!isset($subscription)) abort(410, 'global.token_deleted');

        if (isset($subscription->due_date)) {
            $now = Carbon::now();
            $due_date = Carbon::parse($subscription->due_date);
            if ($due_date < $now) {
                abort(410, 'global.token_expired');
            }
        }

        return $this->show($map, $input['sharing_token']);

    }
    protected function validateRequest()
    {
        return request()->validate([
            'id' => 'sometimes',
            'title' => 'sometimes',
            'subtitle'=> 'sometimes',
            'description'=> 'sometimes',
            'tags'=> 'sometimes',
            'type_id'=> 'sometimes',
            'category_id'=> 'sometimes',
            'border_url'=> 'sometimes',
            'latitude'=> 'sometimes',
            'longitude'=> 'sometimes',
            'zoom'=> 'sometimes',
            'color'=> 'sometimes',
            'medium_id'=> 'sometimes',
            'owner_id' => 'sometimes',
            'sharing_token' => 'sometimes',
        ]);
    }
}

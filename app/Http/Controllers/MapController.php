<?php

namespace App\Http\Controllers;

use App\Map;
use App\Organization;
use App\Videoconference;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

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

    public function userMaps($withOwned = true, $user = null)
    {
        if ($user == null)
        {
            $user = auth()->user();
        }
        $userCanSee = $user->maps;

        foreach ($user->groups as $group) {
            $userCanSee = $userCanSee->merge($group->maps);
        }
        $organization = Organization::find($user->current_organization_id)->maps;
        $userCanSee = $userCanSee->merge($organization);

        if ($withOwned)
        {
            $owned = Map::where('owner_id', $user->id)->get();
            $userCanSee = $userCanSee->merge($owned);
        }

        return $userCanSee->unique();
    }
    public function list(Request $request)
    {
        abort_unless(\Gate::allows('map_access'), 403);

        switch ($request->filter)
        {
            case 'owner':            $maps = Map::where('owner_id', auth()->user()->id)->get();
                break;
            case 'by_organization':  $maps = Organization::where('id', auth()->user()->current_organization_id)->get()->first()->maps;
                break;
            case 'shared_with_me':   $maps = $this->userMaps(false);
                break;
            case 'shared_by_me':     $maps = Map::where('owner_id', auth()->user()->id)->whereHas('subscriptions')->get();
                break;
            case 'all':
            default:                 $maps = $this->userMaps();
                break;
        }

        return empty($maps) ? '' : DataTables::of($maps)
            ->setRowId('id')
            ->make(true);
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
    public function show(Map $map)
    {
        $map = Map::where('id', $map->id)
            ->with(['type', 'category'])
            ->get()
            ->first();
        return view('map.show')
            ->with(compact('map'));
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
            'subtitle'      => $input['subtitle'] ?? $map->subtitle,
            'description'   => $input['description'],
            'tags'          => $input['tags'] ?? $map->tags,
            'type_id'       => $input['type_id'] ?? $map->type_id,
            'category_id'   => $input['category_id'] ?? $map->category_id,
            'border_url'    => $input['border_url'] ?? $map->border_url,
            'latitude'      => $input['latitude'] ?? $map->latitude,
            'longitude'     => $input['longitude'] ?? $map->longitude,
            'zoom'          => $input['zoom'] ?? $map->zoom,
            'color'         => $input['color'] ?? $map->color,
            'medium_id'     => $input['medium_id'],
            'owner_id'      => auth()->user()->id,
        ]);

        $map->save();
        if (request()->wantsJson())
        {
            return $map;
        }
        else {
            return redirect(route('maps.show', ['map' => $map]));
        }
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

        $return = $map->delete();
        if (request()->wantsJson()) {
            return [$return];
        }

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
        ]);
    }
}

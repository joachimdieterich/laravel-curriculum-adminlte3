<?php

namespace App\Http\Controllers;

use App\Map;
use App\MapSubscription;
use App\Organization;
use App\User;
use App\Videoconference;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;

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
            return ['map' => $map];
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
        abort_unless( $map->isAccessible(), 403); // don't use map_show -> bugfix for 403 problem on tokens.

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
            'title' => $input['title'] ?? $map->title,
            'subtitle'=> $input['subtitle'] ?? $map->subtitle,
            'description'=> $input['description'] ?? $map->description,
            'tags'=> $input['tags'] ?? $map->tags,
            'type_id'=> $input['type_id'] ?? $map->type_id,
            'category_id'=> $input['category_id'] ?? $map->category_id,
            'border_url'=> $input['border_url'] ?? $map->border_url,
            'latitude'=> $input['latitude'] ?? $map->latitude,
            'longitude'=> $input['longitude'] ?? $map->longitude,
            'zoom'=> $input['zoom'] ?? $map->zoom,
            'color'=> $input['color'] ?? $map->color,
            'owner_id' => auth()->user()->id,
        ]);

        $map->save();
        if (request()->wantsJson())
        {
            return ['map' => $map];
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

    public function getMapByToken(Map $map, Request $request)
    {
        if (Auth::user() == null) {       //if no user is authenticated authenticate guest
            LogController::set('guestLogin');
            LogController::setStatistics();
            Auth::loginUsingId((env('GUEST_USER')), true);
        }

        $input = $this->validateRequest();

        $subscription = MapSubscription::where('sharing_token',$input['sharing_token'] )->get()->first();
        if ($subscription->due_date) {
            $now = Carbon::now();
            $due_date = Carbon::parse($subscription->due_date);
            if ($due_date < $now) {
                abort(410, 'Dieser Link ist nicht mehr gültig');
            }
        }

        return $this->show($map, $input['sharing_token']);
    }

    protected function validateRequest()
    {
        return request()->validate([
            'id' => 'sometimes|integer',
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
            'sharing_token' => 'sometimes'
        ]);
    }
}

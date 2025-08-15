<?php

namespace App\Http\Controllers;

use App\MapMarker;
use Illuminate\Http\Request;

class MapMarkerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->wantsJson() AND request()->has(['type_id', 'category_id'])) {
            $input = request()->validate([
                'type_id' => 'required|integer',
                'category_id' => 'required|integer',
            ]);
            return MapMarker::where([
                    'type_id' => $input['type_id'],
                    'category_id' => $input['category_id'],
                ])
                ->with(['type', 'category'])
                ->orderBy('type_id')
                ->get();
        } else {
            return MapMarker::orderBy('type_id')->get();
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
        abort_unless(\Gate::allows('map_create'), 403);

        $input = $this->validateRequest();

        $marker = MapMarker::create([
            'title' => $input['title'],
            'teaser_text' => $input['teaser_text'],
            'description' => $input['description'],
            'author' => $input['author'],
            'type_id' => $input['type_id'],
            'category_id' => $input['category_id'],
            'map_id' => $input['map_id'],
            'tags' => $input['tags'],
            'latitude' => $input['latitude'],
            'longitude' => $input['longitude'],
            'address' => $input['address'],
            'url' => $input['url'],
            'url_title' => $input['url_title'],
            'owner_id' => auth()->user()->id,
        ]);

        if (request()->wantsJson()) {
            return $marker->with(['type', 'category'])->find($marker->id);
        }
    }

    /**f
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\MapMarker  $mapMarker
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MapMarker $mapMarker)
    {
        $input = $this->validateRequest();
        abort_unless((\Gate::allows('map_edit')), 403);

        $mapMarker->update([
            'title' => $input['title'] ?? $mapMarker->title,
            'teaser_text' => $input['teaser_text'],
            'description' => $input['description'],
            'author' => $input['author'],
            'type_id' => $input['type_id'] ?? $mapMarker->type_id,
            'category_id' => $input['category_id'] ?? $mapMarker->category_id,
            'map_id' => $input['map_id'] ?? $mapMarker->map_id,
            'tags' => $input['tags'],
            'latitude' => $input['latitude'] ?? $mapMarker->latitude,
            'longitude' => $input['longitude'] ?? $mapMarker->longitude,
            'address' => $input['address'],
            'url' => $input['url'],
            'url_title' => $input['url_title'],
            'owner_id' => auth()->user()->id,
        ]);

        $mapMarker->save();

        if (request()->wantsJson()) {
            return $mapMarker;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\MapMarker  $mapMarker
     * @return \Illuminate\Http\Response
     */
    public function destroy(MapMarker $mapMarker)
    {
        abort_unless((\Gate::allows('map_delete') and $mapMarker->owner_id === auth()->user()->id), 403);

        $mapMarker->mediaSubscriptions()->delete();
        $mapMarker->subscriptions()->delete();

        $return = $mapMarker->delete();
        if (request()->wantsJson()) {
            return [ $return];
        }
    }

    protected function validateRequest()
    {
        return request()->validate([
            'id' => 'sometimes|integer|nullable',
            'title' => 'required|string',
            'teaser_text' => 'sometimes|string|nullable',
            'description' => 'sometimes|string|nullable',
            'author' => 'sometimes|string|nullable',
            'type_id' => 'required|integer',
            'category_id' => 'required|integer',
            'map_id' => 'sometimes|integer|nullable',
            'tags' => 'sometimes|string|nullable',
            'latitude' => 'required|nullable',
            'longitude' => 'required|nullable',
            'address' => 'sometimes|string|nullable',
            'url' => 'sometimes|string|nullable',
            'url_title' => 'sometimes|string|nullable',
            'owner_id' => 'sometimes|integer|nullable',
        ]);
    }
}

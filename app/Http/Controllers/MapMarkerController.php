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
        //abort_unless(\Gate::allows('map_access'), 403);
        if (request()->wantsJson() AND request()->has(['type_id', 'category_id'])) {
            $input = $this->validateRequest();
            return [
                'markers' => MapMarker::where('type_id',  $input['type_id'])
                                ->where('category_id', $input['category_id'])
                                ->orderBy('type_id')
                                ->get()];
        } else {
            return ['markers' => MapMarker::orderBy('type_id')->get()];
        }

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\MapMarker  $mapMarker
     * @return \Illuminate\Http\Response
     */
    public function show(MapMarker $mapMarker)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\MapMarker  $mapMarker
     * @return \Illuminate\Http\Response
     */
    public function edit(MapMarker $mapMarker)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\MapMarker  $mapMarker
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MapMarker $mapMarker)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\MapMarker  $mapMarker
     * @return \Illuminate\Http\Response
     */
    public function destroy(MapMarker $mapMarker)
    {
        //
    }

    protected function validateRequest()
    {
        return request()->validate([
            'id' => 'sometimes|integer|nullable',
            'title' => 'sometimes|string',
            'description' => 'sometimes|nullable',
            'type_id' => 'sometimes|integer',
            'category_id' => 'sometimes|integer',
            'tags' => 'sometimes|string',
            'latitude' => 'sometimes|nullable',
            'longlitude' => 'sometimes|nullable',
            'address' => 'sometimes|nullable',
            'url' => 'sometimes|nullable',
            'owner_id' => 'sometimes|integer',

        ]);
    }
}

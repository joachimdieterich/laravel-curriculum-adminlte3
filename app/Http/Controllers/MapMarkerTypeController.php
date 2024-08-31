<?php

namespace App\Http\Controllers;

use App\MapMarkerType;
use Illuminate\Http\Request;

class MapMarkerTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //abort_unless(\Gate::allows('map_access'), 403);
        if (request()->wantsJson()) {
            return  getEntriesForSelect2ByModel(
                "App\MapMarkerType"
            );
            /*return [
                'mapMarkerTypes' => MapMarkerType::all()
            ];*/
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
     * @param  \App\MapMarkerType  $mapMarkerType
     * @return \Illuminate\Http\Response
     */
    public function show(MapMarkerType $mapMarkerType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\MapMarkerType  $mapMarkerType
     * @return \Illuminate\Http\Response
     */
    public function edit(MapMarkerType $mapMarkerType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\MapMarkerType  $mapMarkerType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MapMarkerType $mapMarkerType)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\MapMarkerType  $mapMarkerType
     * @return \Illuminate\Http\Response
     */
    public function destroy(MapMarkerType $mapMarkerType)
    {
        //
    }
}

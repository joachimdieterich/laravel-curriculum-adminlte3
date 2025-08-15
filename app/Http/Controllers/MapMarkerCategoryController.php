<?php

namespace App\Http\Controllers;

use App\MapMarkerCategory;
use Illuminate\Http\Request;

class MapMarkerCategoryController extends Controller
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
                "App\MapMarkerCategory"
            );

           /* return [
                'mapMarkerCategories' => MapMarkerCategory::with('children')->get()
            ];*/
        } /*else {
            return ['markers' => MapMarkerCategory::all()];
        }*/
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
     * @param  \App\MapMarkerCategory  $mapMarkerCategory
     * @return \Illuminate\Http\Response
     */
    public function show(MapMarkerCategory $mapMarkerCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\MapMarkerCategory  $mapMarkerCategory
     * @return \Illuminate\Http\Response
     */
    public function edit(MapMarkerCategory $mapMarkerCategory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\MapMarkerCategory  $mapMarkerCategory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MapMarkerCategory $mapMarkerCategory)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\MapMarkerCategory  $mapMarkerCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(MapMarkerCategory $mapMarkerCategory)
    {
        //
    }
}

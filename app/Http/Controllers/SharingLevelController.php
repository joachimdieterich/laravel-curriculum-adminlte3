<?php

namespace App\Http\Controllers;

use App\SharingLevel;
use Illuminate\Http\Request;

class SharingLevelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sharingLevel = SharingLevel::all();
        
        return compact('sharingLevel');
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
     * @param  \App\SharingLevel  $sharingLevel
     * @return \Illuminate\Http\Response
     */
    public function show(SharingLevel $sharingLevel)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\SharingLevel  $sharingLevel
     * @return \Illuminate\Http\Response
     */
    public function edit(SharingLevel $sharingLevel)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\SharingLevel  $sharingLevel
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SharingLevel $sharingLevel)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\SharingLevel  $sharingLevel
     * @return \Illuminate\Http\Response
     */
    public function destroy(SharingLevel $sharingLevel)
    {
        //
    }
}

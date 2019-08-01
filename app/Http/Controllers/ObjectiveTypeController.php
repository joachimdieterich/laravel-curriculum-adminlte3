<?php

namespace App\Http\Controllers;

use App\ObjectiveType;
use Illuminate\Http\Request;

class ObjectiveTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return ObjectiveType::all()->toJson();
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
     * @param  \App\ObjectiveType  $objectiveType
     * @return \Illuminate\Http\Response
     */
    public function show(ObjectiveType $objectiveType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ObjectiveType  $objectiveType
     * @return \Illuminate\Http\Response
     */
    public function edit(ObjectiveType $objectiveType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ObjectiveType  $objectiveType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ObjectiveType $objectiveType)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ObjectiveType  $objectiveType
     * @return \Illuminate\Http\Response
     */
    public function destroy(ObjectiveType $objectiveType)
    {
        //
    }
}

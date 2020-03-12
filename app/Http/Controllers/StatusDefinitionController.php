<?php

namespace App\Http\Controllers;

use App\StatusDefinition;
use Illuminate\Http\Request;

class StatusDefinitionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return array
     */
    public function index()
    {
        $status_definitions = StatusDefinition::all();
        // axios call?
        if (request()->wantsJson()){
            return [
                'message' => $status_definitions
            ];
        }
        return $status_definitions;
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
     * @param  \App\StatusDefinition  $status_definition
     * @return \Illuminate\Http\Response
     */
    public function show(StatusDefinition $status_definition)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\StatusDefinition  $status_definition
     * @return \Illuminate\Http\Response
     */
    public function edit(StatusDefinition $status_definition)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\StatusDefinition  $status_definition
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, StatusDefinition $status_definition)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\StatusDefinition  $status_definition
     * @return \Illuminate\Http\Response
     */
    public function destroy(StatusDefinition $status_definition)
    {
        //
    }
}

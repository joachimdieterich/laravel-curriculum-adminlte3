<?php

namespace App\Http\Controllers;

use App\AgendaItem;
use App\AgendaItemType;
use Illuminate\Http\Request;

class AgendaItemTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->wantsJson() AND request()->has(['term', 'page'])) {
            return $this->getEntriesForSelect2();
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
     * @param  \App\AgendaItemType  $agendaItemType
     * @return \Illuminate\Http\Response
     */
    public function show(AgendaItemType $agendaItemType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\AgendaItemType  $agendaItemType
     * @return \Illuminate\Http\Response
     */
    public function edit(AgendaItemType $agendaItemType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\AgendaItemType  $agendaItemType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AgendaItemType $agendaItemType)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\AgendaItemType  $agendaItemType
     * @return \Illuminate\Http\Response
     */
    public function destroy(AgendaItemType $agendaItemType)
    {
        //
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    private function getEntriesForSelect2(): \Illuminate\Http\JsonResponse
    {
        return getEntriesForSelect2ByCollection(AgendaItemType::select(['id', 'title']));
    }
}

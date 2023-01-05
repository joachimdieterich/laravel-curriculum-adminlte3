<?php

namespace App\Http\Controllers;

use App\AgendaItem;
use App\AgendaItemSpeaker;
use Illuminate\Http\Request;

class AgendaItemSpeakerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        abort_unless(\Gate::allows('meeting_create'), 403);
        $new_agenda_item_speaker = $this->validateRequest();
        $agenda_item_speakers = AgendaItemSpeaker::create([
            'user_id' => $new_agenda_item_speaker['user_id'],
            'agenda_item_id' => $new_agenda_item_speaker['agenda_item_id'],
            'title'         => $new_agenda_item_speaker['title'],
        ]);

        if (request()->wantsJson()) {
            return ['speakers' => AgendaItemSpeaker::where('agenda_item_id', $new_agenda_item_speaker['agenda_item_id'])->with(['user'])->get()];
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\AgendaItemSpeaker  $agendaItemSpeaker
     * @return \Illuminate\Http\Response
     */
    public function show(AgendaItemSpeaker $agendaItemSpeaker)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\AgendaItemSpeaker  $agendaItemSpeaker
     * @return \Illuminate\Http\Response
     */
    public function edit(AgendaItemSpeaker $agendaItemSpeaker)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\AgendaItemSpeaker  $agendaItemSpeaker
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AgendaItemSpeaker $agendaItemSpeaker)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\AgendaItemSpeaker  $agendaItemSpeaker
     * @return \Illuminate\Http\Response
     */
    public function destroy(AgendaItemSpeaker $agendaItemSpeaker)
    {
        abort_unless(\Gate::allows('meeting_delete'), 403);

        if (request()->wantsJson() ) {
            $id =  $agendaItemSpeaker->agenda_item_id;
            $agendaItemSpeaker->delete();
            return ['speakers' => AgendaItemSpeaker::where('agenda_item_id', $id)->with(['user'])->get()];
        }
    }

    protected function validateRequest()
    {
        return request()->validate([
            'user_id'        => 'sometimes|integer',
            'agenda_item_id' => 'sometimes|integer',
            'title'          => 'sometimes|string',
        ]);
    }
}

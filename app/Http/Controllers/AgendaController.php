<?php

namespace App\Http\Controllers;

use App\Agenda;
use App\AgendaItem;
use Illuminate\Http\Request;

class AgendaController extends Controller
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
        $new_agenda = $this->validateRequest();

        $agenda = Agenda::create([
            'meeting_date_id' => $new_agenda['meeting_date_id'],
            'title'           => $new_agenda['title'],
            'description'     => $new_agenda['description'],

            'owner_id'        => auth()->user()->id,
        ]);

        if (request()->wantsJson()) {
            return ['agenda' => $agenda];
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Agenda  $agenda
     * @return \Illuminate\Http\Response
     */
    public function show(Agenda $agenda)
    {
        //return $agenda->with('items')->get();
        if (request()->wantsJson()) {
            $items = AgendaItem::where([
                'agenda_id' => $agenda->id,
            ])->with(['type', 'subscriptions'  => function ($query)  {
                $query->where('subscribable_id', auth()->user()->id);
                $query->where('subscribable_Type', "App\\User");
            },])->get();
            return ['items' => $items];
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Agenda  $agenda
     * @return \Illuminate\Http\Response
     */
    public function edit(Agenda $agenda)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Agenda  $agenda
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Agenda $agenda)
    {
        abort_unless(\Gate::allows('meeting_edit'), 403);
        $input = $this->validateRequest();

        $agenda->update([
            'meeting_date_id' => $input['meeting_date_id'],
            'title'           => $input['title'],
            'description'     => $input['description'],
        ]);

        if (request()->wantsJson()) {
            return ['agenda' => $agenda];
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Agenda  $agenda
     * @return \Illuminate\Http\Response
     */
    public function destroy(Agenda $agenda)
    {
        $agenda->delete();
    }

    protected function validateRequest()
    {
        return request()->validate([
            'id'              => 'sometimes',
            'meeting_date_id' => 'sometimes',
            'title'           => 'sometimes|required',
            'description'     => 'sometimes',
            'owner_id'        => 'sometimes',
        ]);
    }
}

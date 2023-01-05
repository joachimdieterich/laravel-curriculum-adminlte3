<?php

namespace App\Http\Controllers;

use App\Agenda;
use App\AgendaItem;
use DB;
use Illuminate\Http\Request;

class AgendaItemController extends Controller
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
        $new_agenda_item = $this->validateRequest();
        $order_id = $this->getMaxOrderId($new_agenda_item['agenda_item_type_id']);
        $agenda_item = AgendaItem::create([
            'agenda_id' => $new_agenda_item['agenda_id'],
            'agenda_item_type_id' => $new_agenda_item['agenda_item_type_id'],
            'title'         => $new_agenda_item['title'],
            'subtitle'      => $new_agenda_item['subtitle'],
            'description'   => $new_agenda_item['description'],
            'medium_id'     => $new_agenda_item['medium_id'] ?? null,
            'begin'         => $new_agenda_item['begin'],
            'end'           => $new_agenda_item['end'],
            'order_id'      => $new_agenda_item['order_id'] ?? 0,
            'owner_id'      => auth()->user()->id
        ]);

        if (request()->wantsJson()) {
            return ['agendaItem' => $agenda_item];
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\AgendaItem  $agendaItem
     * @return \Illuminate\Http\Response
     */
    public function show(AgendaItem $agendaItem)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\AgendaItem  $agendaItem
     * @return \Illuminate\Http\Response
     */
    public function edit(AgendaItem $agendaItem)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\AgendaItem  $agendaItem
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AgendaItem $agendaItem)
    {
        abort_unless(\Gate::allows('meeting_edit'), 403);
        $input = $this->validateRequest();

        $agendaItem->update([
            'agenda_id' => $input['agenda_id'],
            'agenda_item_type_id' => $input['agenda_item_type_id'],
            'title'         => $input['title'],
            'subtitle'      => $input['subtitle'],
            'description'   => $input['description'],
            'medium_id'     => $input['medium_id'] ?? null,
            'begin'         => $input['begin'],
            'end'           => $input['end'],
            'order_id'      => $input['order_id'] ?? 0,
        ]);

        if (request()->wantsJson()) {
            return ['agenda' => $agendaItem];
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\AgendaItem  $agendaItem
     * @return \Illuminate\Http\Response
     */
    public function destroy(AgendaItem $agendaItem)
    {
        if (request()->wantsJson()) {
            return ['message' => $agendaItem->delete()];
        }
    }

    protected function getMaxOrderId($agenda_id)
    {
        $order_id = DB::table('agenda_items')
            ->where('agenda_id', $agenda_id)
            ->max('order_id');

        return (is_numeric($order_id)) ? $order_id + 1 : 0;
    }

    protected function validateRequest()
    {
        return request()->validate([
            'id'  => 'sometimes',
            'agenda_id' => 'sometimes|integer',
            'agenda_item_type_id' => 'sometimes|integer',
            'title' => 'sometimes|string',
            'subtitle'=> 'sometimes',
            'description'=> 'sometimes',
            'medium_id' => 'sometimes|integer|nullable',
            'begin' => 'sometimes',
            'end' => 'sometimes',
            'order_id' => 'sometimes|required|integer',
            'owner_id' => 'sometimes|integer|nullable',
        ]);
    }
}

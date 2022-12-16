<?php

namespace App\Http\Controllers;

use App\AgendaItem;
use App\Agenda;
use App\AgendaItemSubscription;
use Illuminate\Http\Request;

class AgendaItemSubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->wantsJson()) {

            if (request('agenda_id'))
            {
                $items = AgendaItem::whereIn('agenda_id', array(request('agenda_id')))
                    ->with(['type', 'subscriptions'  => function ($query)  {
                        $query->where('subscribable_id', auth()->user()->id);
                        $query->where('subscribable_Type', "App\\User");
                    },])->get();
            }
            else if (request('meeting_date_id'))
            {
                $items = AgendaItem::whereIn('agenda_id', Agenda::where('meeting_date_id', request('meeting_date_id'))->get()->pluck('id')->toArray())
                    ->with(['type', 'subscriptions'])
                    ->whereHas("subscriptions", function ($query)  {
                        $query->where('subscribable_id', auth()->user()->id);
                        $query->where('subscribable_Type', "App\\User");
                    })->get();
            }

            return ['items' => $items];
        }

    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $this->validateRequest();
        $agendaItem = AgendaItem::find($input['agenda_item_id']);
        abort_unless((\Gate::allows('agenda_create') and $agendaItem->isAccessible()), 403);

        $subscribe = AgendaItemSubscription::updateOrCreate([
            'agenda_item_id' => $input['agenda_item_id'],
            'subscribable_type' => $input['subscribable_type'],
            'subscribable_id' => $input['subscribable_id'],
        ], [
            'editable' => isset($input['editable']) ? $input['editable'] : false,
            'owner_id' => auth()->user()->id,
        ]);
        $subscribe->save();

        if (request()->wantsJson()) {
            return ['subscription' => $agendaItem->subscriptions()->with('subscribable')->get()];
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\AgendaItemSubscription  $agendaItemSubscription
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AgendaItemSubscription $agendaItemSubscription)
    {
        abort_unless((\Gate::allows('agenda_edit') and $agendaItemSubscription->isAccessible()), 403);
        $input = $this->validateRequest();

        $agendaItemSubscription->update([
            'editable'=> isset($input['editable']) ? $input['editable'] : false,
            'owner_id'=> auth()->user()->id,
        ]);

        if (request()->wantsJson()) {
            return ['editable' => $agendaItemSubscription->editable];
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\AgendaItemSubscription  $agendaItemSubscription
     * @return \Illuminate\Http\Response
     */
    public function destroy(AgendaItemSubscription $agendaItemSubscription)
    {
        abort_unless((\Gate::allows('agenda_delete') and $agendaItemSubscription->isAccessible()), 403);

        if (request()->wantsJson()) {
            return ['message' => $agendaItemSubscription->delete()];
        }
    }

    protected function validateRequest()
    {
        return request()->validate([
            'subscribable_type' => 'sometimes|string',
            'subscribable_id'   => 'sometimes|integer',
            'agenda_item_id'          => 'sometimes|integer',
            'editable'          => 'sometimes',
        ]);
    }
}

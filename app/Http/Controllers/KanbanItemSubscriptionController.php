<?php

namespace App\Http\Controllers;

use App\KanbanItem;
use App\KanbanItemSubscription;
use Illuminate\Http\Request;

class KanbanItemSubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $kanbanItem = KanbanItem::find(request('kanbanItem_id'));
        abort_unless((\Gate::allows('kanban_access') and $kanbanItem->isAccessible()), 403);

        if (request()->wantsJson()) {
            return [
                'subscribers' => [
                    'subscriptions' => $kanbanItem->subscriptions()->with('subscribable')->get(),
                ],
            ];
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
        $kanbanItem = KanbanItem::find($input['model_id']);
        abort_unless((\Gate::allows('kanban_create') and $kanbanItem->isAccessible()), 403);

        $subscribe = KanbanItemSubscription::updateOrCreate([
            'kanban_item_id' => $input['model_id'],
            'subscribable_type' => $input['subscribable_type'],
            'subscribable_id' => $input['subscribable_id'],
        ], [
            'editable' => isset($input['editable']) ? $input['editable'] : false,
            'owner_id' => auth()->user()->id,
        ]);
        $subscribe->save();

        if (request()->wantsJson()) {
            return ['subscription' => $kanbanItem->subscriptions()->with('subscribable')->get()];
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\KanbanItemSubscription  $kanbanItemSubscription
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, KanbanItemSubscription $kanbanItemSubscription)
    {
        abort_unless((\Gate::allows('kanban_edit') and $kanbanItemSubscription->isAccessible()), 403);
        $input = $this->validateRequest();

        $kanbanItemSubscription->update([
            'editable'=> isset($input['editable']) ? $input['editable'] : false,
            'owner_id'=> auth()->user()->id,
        ]);

        if (request()->wantsJson()) {
            return ['editable' => $kanbanItemSubscription->editable];
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\KanbanItemSubscription  $kanbanItemSubscription
     * @return \Illuminate\Http\Response
     */
    public function destroy(KanbanItemSubscription $kanbanItemSubscription)
    {
        abort_unless((\Gate::allows('kanban_delete') and $kanbanItemSubscription->isAccessible()), 403);

        if (request()->wantsJson()) {
            return ['message' => $kanbanItemSubscription->delete()];
        }
    }

    protected function validateRequest()
    {
        return request()->validate([
            'subscribable_type' => 'sometimes|string',
            'subscribable_id'   => 'sometimes|integer',
            'model_id'          => 'sometimes|integer',
            'editable'          => 'sometimes',
        ]);
    }
}

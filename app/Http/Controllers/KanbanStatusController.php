<?php

namespace App\Http\Controllers;

use App\Kanban;
use App\KanbanItem;
use App\KanbanStatus;
use Illuminate\Http\Request;

class KanbanStatusController extends Controller
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
        $input = $this->validateRequest();
        abort_unless((\Gate::allows('kanban_create') and Kanban::find($input['kanban_id'])->isAccessible()), 403);

        $kanban = KanbanStatus::firstOrCreate([
            'title' => $input['title'],
            'order_id' => $input['order_id'],
            'kanban_id' => $input['kanban_id'],

            'owner_id' => auth()->user()->id,
        ]);

        // axios call?
        if (request()->wantsJson()) {
            return ['message' => $kanban];
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\KanbanStatus  $kanbanStatus
     * @return \Illuminate\Http\Response
     */
    public function show(KanbanStatus $kanbanStatus)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\KanbanStatus  $kanbanStatus
     * @return \Illuminate\Http\Response
     */
    public function edit(KanbanStatus $kanbanStatus)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\KanbanStatus  $kanbanStatus
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, KanbanStatus $kanbanStatus)
    {
        //
    }

    public function sync(Request $request)
    {
        $this->validate(request(), [
            'columns' => ['required', 'array'],
        ]);
        $kanban_id = $request->columns[0]['kanban_id'];
        abort_unless((\Gate::allows('kanban_show') and Kanban::find($kanban_id)->isAccessible()), 403);

        foreach ($request->columns as $order_id => $status) {
            if ($status['order_id'] !== $order_id) {
                KanbanStatus::where('kanban_id', '=', $status['kanban_id'])
                    ->where('order_id', '>=', $order_id)->increment('order_id');
            }
            KanbanStatus::find($status['id'])
                ->update(['order_id' => $order_id]);
        }

        $kanban_id = $request->columns[0]['kanban_id'];
        if (request()->wantsJson()) {
            return ['message' => Kanban::where('id', $kanban_id)->with(['statuses', 'statuses.items' => function ($query) use ($kanban_id) {
                $query->where('kanban_id', $kanban_id)->with(['owner', 'taskSubscription.task.subscriptions' => function ($query) {
                    $query->where('subscribable_id', auth()->user()->id)
                        ->where('subscribable_type', 'App\User');
                }, 'mediaSubscriptions.medium'])->orderBy('order_id');
            }, 'statuses.items.subscribable'])->get()->first()->statuses];
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\KanbanStatus  $kanbanStatus
     * @return \Illuminate\Http\Response
     */
    public function destroy(KanbanStatus $kanbanStatus)
    {
        abort_unless((\Gate::allows('kanban_delete') and $kanbanStatus->isAccessible()), 403);

        if ($kanbanStatus->items()->count() <= 1) {
            KanbanItem::where('kanban_status_id', $kanbanStatus->id)->delete();
        }

        if (request()->wantsJson()) {
            return ['message' => $kanbanStatus->delete()];
        }
    }

    protected function validateRequest()
    {
        return request()->validate([
            'title' => 'sometimes|required',
            'kanban_id' => 'sometimes|required|integer',
            'order_id' => 'sometimes|required|integer',
        ]);
    }
}

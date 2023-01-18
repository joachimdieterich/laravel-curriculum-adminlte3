<?php

namespace App\Http\Controllers;

use App\Kanban;
use App\KanbanItem;
use Illuminate\Http\Request;

class KanbanItemController extends Controller
{
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

        $kanbanItem = KanbanItem::firstOrCreate([
            'title'             => $input['title'],
            'description'       => $input['description'],
            'order_id'          => $input['order_id'],
            'kanban_id'         => $input['kanban_id'],
            'kanban_status_id'  => $input['kanban_status_id'],
            'color'             => $input['color'],
            'owner_id'          => auth()->user()->id,
        ]);

        LogController::set(get_class($this).'@'.__FUNCTION__);

        // axios call?
        if (request()->wantsJson()) {
            return ['message' => KanbanItem::where('id', $kanbanItem->id)->with(['mediaSubscriptions', 'media', 'owner', 'taskSubscription', 'comments'])->get()->first()];
        }
    }

    public function sync(Request $request)
    {
        $this->validate(request(), [
            'columns' => ['required', 'array'],
        ]);
        $kanban_id = $request->columns[0]['kanban_id'];
        abort_unless((\Gate::allows('kanban_show') and Kanban::find($kanban_id)->isAccessible()), 403);

        foreach ($request->columns as $kanban_status) {
            foreach ($kanban_status['items'] as $order_id => $item) {
                if ($item['kanban_status_id'] !== $kanban_status['id'] || $item['order_id'] !== $order_id) {
                    if ($item['kanban_status_id'] !== $kanban_status['id']) {
                        KanbanItem::where('kanban_status_id', '=', $item['kanban_status_id'])
                            ->where('order_id', '>', $item['order_id'])->decrement('order_id');
                    }
                    KanbanItem::where('kanban_status_id', '=', $kanban_status['id'])
                        ->where('order_id', '>=', $order_id)->increment('order_id');

                    //update  set order_id +1 where $kanban_status['id'] and order_id >= $order_id
                    KanbanItem::find($item['id'])
                        ->update(['kanban_status_id' => $kanban_status['id'], 'order_id' => $order_id]);
                }
            }
        }

        LogController::set(get_class($this).'@'.__FUNCTION__);

        if (request()->wantsJson()) {
            return ['message' => Kanban::with(['statuses', 'statuses.items' => function ($query) use ($kanban_id) {
                $query->where('kanban_id', $kanban_id)->with(['owner', 'taskSubscription.task.subscriptions' => function ($query) {
                    $query->where('subscribable_id', auth()->user()->id)
                        ->where('subscribable_type', 'App\User');
                }, 'mediaSubscriptions.medium'])->orderBy('order_id');
            }, 'statuses.items.subscribable', 'statuses.items.comments', 'statuses.items.comments.user'])->where('id', $kanban_id)->get()->first()->statuses];
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\KanbanItem  $kanbanItem
     * @return \Illuminate\Http\Response
     */
    public function show(KanbanItem $kanbanItem)
    {
        abort_unless((\Gate::allows('kanban_show') and $kanbanItem->isAccessible()), 403);
        if (request()->wantsJson()) {
            return $this->getItemWithRelations($kanbanItem);
        }

        return redirect()->action('KanbanController@show', ['kanban' => $kanbanItem->kanban_id]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\KanbanItem  $kanbanItem
     * @return \Illuminate\Http\Response
     */
    public function edit(KanbanItem $kanbanItem)
    {
        abort_unless((\Gate::allows('kanban_edit') and $kanbanItem->isAccessible()), 403);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\KanbanItem  $kanbanItem
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, KanbanItem $kanbanItem)
    {
        abort_unless((\Gate::allows('kanban_edit') and $kanbanItem->isAccessible()), 403);

        $input = $this->validateRequest();

        $kanbanItem->update([
            'title' => $input['title'],
            'description' => $input['description'],
            'order_id' => $input['order_id'],
            'kanban_id' => $input['kanban_id'],
            'kanban_status_id' => $input['kanban_status_id'],
            'color' => $input['color'],
            'owner_id' => auth()->user()->id,
        ]);

        // axios call?
        if (request()->wantsJson()) {
            return $this->getItemWithRelations($kanbanItem);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\KanbanItem  $kanbanItem
     * @return \Illuminate\Http\Response
     */
    public function destroy(KanbanItem $kanbanItem)
    {
        abort_unless((\Gate::allows('kanban_delete') and $kanbanItem->isAccessible()), 403);

        $kanbanItem->mediaSubscriptions()->delete();
        $kanbanItem->subscriptions()->delete();

        if (request()->wantsJson()) {
            return ['message' => $kanbanItem->delete()];
        }
    }

    protected function validateRequest()
    {
        return request()->validate([
            'title' => 'sometimes|required',
            'description' => 'sometimes',
            'order_id' => 'sometimes|required|integer',
            'kanban_id' => 'sometimes|required|integer',
            'kanban_status_id' => 'sometimes|required|integer',
            'color' => 'sometimes',
        ]);
    }

    /**
     * @param  KanbanItem  $kanbanItem
     * @return array
     */
    private function getItemWithRelations(KanbanItem $kanbanItem): array
    {
        return ['message' => $kanbanItem->with(
            ['owner', 'mediaSubscriptions.medium', 'taskSubscription.task.subscriptions' => function ($query) {
                $query->where('subscribable_id', auth()->user()->id)
                    ->where('subscribable_type', 'App\User');
            }, 'subscribable'])->where('id', $kanbanItem->id)->get()->first()];
    }
}

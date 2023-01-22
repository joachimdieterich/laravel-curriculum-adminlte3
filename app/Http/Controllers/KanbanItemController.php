<?php

namespace App\Http\Controllers;

use App\Kanban;
use App\KanbanItem;
use Illuminate\Support\Facades\DB;
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

        $order_id = DB::table('kanban_items')
            ->where('kanban_id', $input['kanban_id'])
            ->where('kanban_status_id', $input['kanban_status_id'])
            ->max('order_id');

        $kanbanItem = KanbanItem::firstOrCreate([
            'title'             => $input['title'],
            'description'       => $input['description'],
            'order_id'          => $order_id ?? 0,
            'kanban_id'         => $input['kanban_id'],
            'kanban_status_id'  => $input['kanban_status_id'],
            'color'             => $input['color'],
            'owner_id'          => auth()->user()->id,
        ]);

        LogController::set(get_class($this).'@'.__FUNCTION__);

        // axios call?
        if (request()->wantsJson()) {

            event(new \App\Events\KanbanItemAddedEvent($kanbanItem));

            return ['message' => KanbanItem::where('id', $kanbanItem->id)
                ->with(['mediaSubscriptions', 'media', 'owner', /*'taskSubscription',*/ 'comments'])
                ->get()->first()];
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
            event(new \App\Events\KanbanItemMovedEvent($request->columns));

            return ['message' =>  (new KanbanController)->getKanbanWithRelations(Kanban::find($kanban_id))];
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
            Kanban::find($kanbanItem->kanban_id)->touch('updated_at'); //To get Sync after media upload working

            event(new \App\Events\KanbanItemReloadEvent($kanbanItem));

            return $this->getItemWithRelations($kanbanItem);
        }

        return redirect()->action('KanbanController@show', ['kanban' => $kanbanItem->kanban_id]);
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
            event(new \App\Events\KanbanItemUpdatedEvent($kanbanItem));

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

        $kanbanItemForEvent = $kanbanItem;

        $kanbanItem->mediaSubscriptions()->delete();
        $kanbanItem->subscriptions()->delete();

        if (request()->wantsJson()) {
            event(new \App\Events\KanbanItemDeletedEvent($kanbanItemForEvent));

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
            ['owner', 'mediaSubscriptions.medium',
                /*'taskSubscription.task.subscriptions' => function ($query) {
                    $query->where('subscribable_id', auth()->user()->id)
                        ->where('subscribable_type', 'App\User');
                }, */
                'subscribable'])->where('id', $kanbanItem->id)->get()->first()];
    }

}

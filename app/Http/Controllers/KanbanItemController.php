<?php

namespace App\Http\Controllers;

use App\Kanban;
use App\KanbanItem;
use App\MediumSubscription;
use Maize\Markable\Models\Like;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        abort_unless((\Gate::allows('kanban_edit') and Kanban::find($input['kanban_id'])->isAccessible()), 403);

        $order_id = DB::table('kanban_items')
            ->where('kanban_id', $input['kanban_id'])
            ->where('kanban_status_id', $input['kanban_status_id'])
            ->max('order_id');

        $kanbanItem = KanbanItem::firstOrCreate([
            'title'             => $input['title'],
            'description'       => $input['description'],
            'order_id'          => ($order_id === NULL) ? 0 : $order_id + 1,
            'kanban_id'         => $input['kanban_id'],
            'kanban_status_id'  => $input['kanban_status_id'],
            'color'             => $input['color'],
            'due_date'          => $input['due_date'],
            'locked' => $input['locked'] ?? false,
            'editable' => $input['editable'] ?? true,
            'visibility' => $input['visibility'] ?? true,
            'visible_from' => $input['visible_from'],
            'visible_until' => $input['visible_until'],
            'owner_id'          => auth()->user()->id,
        ]);

        LogController::set(get_class($this).'@'.__FUNCTION__);
        Kanban::find($input['kanban_id'])->touch('updated_at'); //To get Sync after media upload working


        if (request()->wantsJson()) {

            if (!pusher_event(new \App\Events\Kanbans\KanbanItemAddedEvent($kanbanItem)))
            {
                return [
                    'message' =>  KanbanItem::where('id', $kanbanItem->id)
                        ->with([
                            'comments',
                            'comments.user',
                            'comments.likes',
                            'likes',
                            'mediaSubscriptions.medium',
                            'owner',
                        ])
                        ->get()->first()
                ];
            }
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
            if (!pusher_event(new \App\Events\Kanbans\KanbanItemMovedEvent($request->columns)))
            {
                return [
                    'message' => $request->columns
                ];
            }
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
            if (!pusher_event(new \App\Events\Kanbans\KanbanItemReloadEvent($kanbanItem)))
            {
                return [
                    'user' => auth()->user()->only(['id', 'firstname', 'lastname']),
                    'message' =>  $kanbanItem
                        ->where('id', $kanbanItem->id)
                        ->with([
                            'comments',
                            'comments.user',
                            'comments.likes',
                            'likes',
                            'mediaSubscriptions.medium',
                            'owner',
                            ])
                        ->get()->first()
                ];
            }
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
            'due_date' => $input['due_date'],
            'locked' => $input['locked'] ?? false,
            'editable' => $input['editable'] ?? true,
            'visibility' => $input['visibility'] ?? true,
            'visible_from' => $input['visible_from'] ?? NULL,
            'visible_until' => $input['visible_until'] ?? NULL,
            'owner_id' => $kanbanItem->owner_id, //owner should not be updated
            'editors_ids' => array_merge($kanbanItem->editors_ids, [auth()->user()->id] )
        ]);

        if (request()->wantsJson()) {
            if (!pusher_event(new \App\Events\Kanbans\KanbanItemUpdatedEvent($kanbanItem)))
            {
                return [
                    'user' => auth()->user()->only(['id', 'firstname', 'lastname']),
                    'message' =>  $kanbanItem
                ];
            }
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

        Kanban::find($kanbanItem->kanban_id)->touch('updated_at'); //To get Sync after media upload working

        $kanbanItemForEvent = $kanbanItem;

        $kanbanItem->mediaSubscriptions()->delete();
        $kanbanItem->subscriptions()->delete();
        $kanbanItem->delete();

        if (request()->wantsJson()) {
            if (!pusher_event(new \App\Events\Kanbans\KanbanItemDeletedEvent($kanbanItemForEvent)))
            {
                return [
                    'user' => auth()->user()->only(['id', 'firstname', 'lastname']),
                    'message' =>  $kanbanItemForEvent
                ];
            }
        }
    }

    public function copyItem(KanbanItem $item, Request $request)
    {
        $order_id = DB::table('kanban_items')
            ->where('kanban_id', $item->kanban_id)
            ->where('kanban_status_id', $item->kanban_status_id)
            ->max('order_id');

        $itemCopy = KanbanItem::Create([
            'title'             => '[Kopie] ' . $item->title,
            'description'       => $item->description,
            'order_id'          => $order_id + 1,
            'kanban_id'         => $item->kanban_id,
            'kanban_status_id'  => $item->kanban_status_id,
            'color'             => $item->color,
            'owner_id'          => auth()->user()->id,
        ]);

        foreach ($item->mediaSubscriptions as $mediaSubscription) {
            MediumSubscription::Create([
                'medium_id'         => $mediaSubscription->medium_id,
                'subscribable_type' => $mediaSubscription->subscribable_type,
                'subscribable_id'   => $itemCopy->id,
                'sharing_level_id'  => $mediaSubscription->sharing_level_id,
                'visibility'        => $mediaSubscription->visibility,
                'additional_data'   => $mediaSubscription->additional_data,
                'owner_id'          => auth()->user()->id,
            ]);
        }

        return [
            'message' => KanbanItem::Where('id', $itemCopy->id)
                ->with([
                    'comments',
                    'comments.user',
                    'comments.likes',
                    'likes',
                    'mediaSubscriptions.medium',
                    'owner',
                ])
                ->get()->first()
        ];
    }

    /**
     * React to kanbanItem the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\KanbanItem  $kanbanItem
     * @return \Illuminate\Http\Response
     */
    public function reaction(Request $request, KanbanItem $kanbanItem)
    {
        abort_unless( $kanbanItem->isAccessible(), 403);

        $input = $this->validateRequest();
        //todo: use reaction_type 'like'...
        if (Like::has($kanbanItem, auth()->user())){
            Like::remove($kanbanItem, auth()->user()); // unmarks the $kanbanItem liked for the given user
        } else {
            Like::add($kanbanItem, auth()->user()); // marks the $kanbanItem liked for the given user
        }

        if (request()->wantsJson()) {
            if (!pusher_event(new \App\Events\Kanbans\KanbanItemUpdatedEvent($kanbanItem)))
            {
                return [
                    'user' => auth()->user()->only(['id', 'firstname', 'lastname']),
                    'message' => KanbanItem::where('id', $kanbanItem->id)
                        ->with([
                            'likes',
                        ])->get()->first(),
                ];
            }
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\KanbanItem  $kanbanItem
     * @return \Illuminate\Http\Response
     */
    public function editors(KanbanItem $kanbanItem)
    {
        abort_unless((\Gate::allows('kanban_show') and $kanbanItem->isAccessible()), 403);

        if (request()->wantsJson()) {
            return [
                'editors' =>  $kanbanItem->editors(['id', 'username', 'firstname', 'lastname'])
            ];
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
            'due_date' => 'sometimes',
            'locked' => 'sometimes|boolean',
            'editable' => 'sometimes|boolean',
            'visibility' => 'sometimes|boolean',
            'visible_from' => 'sometimes',
            'visible_until' => 'sometimes',
            'editors_id' => 'sometimes',
        ]);
    }

}

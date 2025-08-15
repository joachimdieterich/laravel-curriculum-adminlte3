<?php

namespace App\Http\Controllers;

use App\Kanban;
use App\KanbanItem;
use App\KanbanStatus;
use App\MediumSubscription;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KanbanStatusController extends Controller
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

        $order_id = DB::table('kanban_statuses')
            ->where('kanban_id', $input['kanban_id'])
            ->max('order_id');

        $kanbanStatus = KanbanStatus::firstOrCreate([
            'title' => $input['title'],
            'order_id' => ($order_id === NULL) ? 0 : $order_id + 1,
            'kanban_id' => $input['kanban_id'],
            'locked' => $input['locked'] ?? false,
            'editable' => $input['editable'] ?? true,
            'visibility' => $input['visibility'] ?? true,
            'visible_from' => $input['visible_from'] ?? NULL,
            'visible_until' => $input['visible_until'] ?? NULL,
            'owner_id' => auth()->user()->id,
        ]);

        if (request()->wantsJson()) {
            if (!pusher_event(new \App\Events\Kanbans\KanbanStatusAddedEvent($kanbanStatus)))
            {
                return [
                    'user' => auth()->user()->only(['id', 'firstname', 'lastname']),
                    'message' =>  $kanbanStatus
                ];
            }
        }
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
        abort_unless((\Gate::allows('kanban_edit') and  Kanban::find($kanbanStatus->kanban_id)->isAccessible()), 403);

        $input = $this->validateRequest();

        $kanbanStatus->update([
            'title' => $input['title'],
            'locked' => $input['locked'] ?? false,
            'editable' => $input['editable'] ?? true,
            'visibility' => $input['visibility'] ?? true,
            'visible_from' => $input['visible_from'],
            'visible_until' => $input['visible_until'],
            'owner_id' => $kanbanStatus->owner_id, //owner should not be updated
            'editors_ids' => array_merge($kanbanStatus->editors_ids, [auth()->user()->id] )
        ]);

        if (request()->wantsJson()) {
            if (!pusher_event(new \App\Events\Kanbans\KanbanStatusUpdatedEvent($kanbanStatus)))
            {
                return [
                    'user' => auth()->user()->only(['id', 'firstname', 'lastname']),
                    'message' =>  $kanbanStatus
                ];
            }
        }
    }

    public function checkSync(Kanban $kanban){
        $date = new DateTime;
        $date->modify('-10 seconds');
        $formatted_date = $date->format('Y-m-d H:i:s');

        $update_kanban = Kanban::where('id', $kanban->id)
            ->where('updated_at','>=',$formatted_date)->get();
        $new_statuses = KanbanStatus::where('kanban_id', $kanban->id)
            ->where('created_at','>=',$formatted_date)
            ->orWhere('updated_at','>=',$formatted_date)->get();
        $new_items = KanbanItem::where('kanban_id', $kanban->id)
            ->where('created_at','>=',$formatted_date)
            ->orWhere('updated_at','>=',$formatted_date)->get();


        if (request()->wantsJson()) {
            if ($update_kanban->count() !== 0 OR $new_statuses->count() !== 0 OR $new_items->count() !== 0) {
                return ['message' => (new KanbanController)->getKanbanWithRelations($kanban)];
            } else {
                return ['message' => 'uptodate'];
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
            $kanban = Kanban::find($kanban_id);
            if (!pusher_event(new \App\Events\Kanbans\KanbanStatusMovedEvent($kanban)))
            {
                return [
                    'user' => auth()->user()->only(['id', 'firstname', 'lastname']),
                    'message' => $kanban
                        ->where('id', $kanban->id)
                        ->with(['statuses'])
                        ->get()->first()
                ];
            }
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

        $kanbanStatusForEvent = $kanbanStatus;

        $kanbanStatus->items()->delete();
        Kanban::find($kanbanStatus->kanban_id)->touch('updated_at'); //To get Sync working

        $kanbanStatus->delete();

        if (request()->wantsJson()) {
            if (!pusher_event(new \App\Events\Kanbans\KanbanStatusDeletedEvent($kanbanStatusForEvent)))
            {
                return [
                    'user' => auth()->user()->only(['id', 'firstname', 'lastname']),
                    'message' =>  $kanbanStatusForEvent
                ];
            }

        }
    }

    public function copyStatus(KanbanStatus $status, Request $request)
    {
        $order_id = DB::table('kanban_statuses')
            ->where('kanban_id', $status->kanban_id)
            ->max('order_id');

        $statusCopy = KanbanStatus::Create([
            'title'     => '[Kopie] ' . $status->title,
            'order_id'  => $order_id + 1,
            'kanban_id' => $status['kanban_id'],
            'owner_id'  => auth()->user()->id,
        ]);

        foreach ($status->items as $item) {
            $itemCopy = KanbanItem::Create([
                'title'             => $item->title,
                'description'       => $item->description,
                'order_id'          => $item->order_id,
                'kanban_id'         => $statusCopy->kanban_id,
                'kanban_status_id'  => $statusCopy->id,
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
        }

        return [
            'message' => KanbanStatus::Where('id', $statusCopy->id)
                ->with([
                    'items',
                    'items.comments',
                    'items.comments.user',
                    'items.comments.likes',
                    'items.likes',
                    'items.mediaSubscriptions.medium',
                    'items.owner',
                ])
                ->get()->first()
        ];
    }

    protected function validateRequest()
    {
        return request()->validate([
            'title' => 'sometimes|required',
            'kanban_id' => 'sometimes|required|integer',
            'order_id' => 'sometimes|integer',
            'locked' => 'sometimes|boolean',
            'editable' => 'sometimes|boolean',
            'visibility' => 'sometimes|boolean',
            'visible_from' => 'sometimes',
            'visible_until' => 'sometimes',
            'editors_id' => 'sometimes',
        ]);
    }


    private function getStatusWithRelations(KanbanStatus $kanbanStatus): array
    {
        return ['message' =>  $kanbanStatus
            ->with(['items' => function ($query) use ($kanbanStatus) {
                $query->where('kanban_id', $kanbanStatus->kanban_id)
                    ->with(['owner', 'mediaSubscriptions.medium'])
                    ->orderBy('order_id');
            }, 'items.subscriptions', 'items.comments', 'items.comments.user',
            ])->where('id', $kanbanStatus->id)->get()->first()
        ];
    }
}

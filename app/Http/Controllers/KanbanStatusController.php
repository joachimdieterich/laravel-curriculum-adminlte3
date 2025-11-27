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
     * @param Request $request
     * @return KanbanStatus|void
     */
    public function store(Request $request)
    {
        $input = $this->validateRequest();
        abort_unless(\Gate::allows('kanban_edit') and Kanban::find($input['kanban_id'])->isEditable(null, $this->getCurrentToken()), 403);

        $order_id = DB::table('kanban_statuses')
            ->where('kanban_id', $input['kanban_id'])
            ->max('order_id');

        $kanbanStatus = KanbanStatus::firstOrCreate([
            'title' => $input['title'],
            'order_id' => ($order_id === NULL) ? 0 : $order_id + 1,
            'kanban_id' => $input['kanban_id'],
            'color'   => $input['color'],
            'locked' => $input['locked'] ?? false,
            'editable' => $input['editable'] ?? true,
            'visibility' => $input['visibility'] ?? true,
            'visible_from' => $input['visible_from'] ?? NULL,
            'visible_until' => $input['visible_until'] ?? NULL,
            'owner_id' => auth()->user()->id,
        ]);
        Kanban::find($input['kanban_id'])->touch('updated_at'); //To get Sync after media upload working

        if (request()->wantsJson()) {
            return $kanbanStatus;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param KanbanStatus $kanbanStatus
     * @return KanbanStatus|void
     */
    public function update(Request $request, KanbanStatus $kanbanStatus)
    {
        abort_unless((\Gate::allows('kanban_edit') and $kanbanStatus->isEditable(null, $this->getCurrentToken())), 403);

        $input = $this->validateRequest();

        $kanbanStatus->update([
            'title' => $input['title'],
            'locked' => $input['locked'] ?? false,
            'editable' => $input['editable'] ?? true,
            'visibility' => $input['visibility'] ?? true,
            'visible_from' => $input['visible_from'],
            'visible_until' => $input['visible_until'],
            'color'         => $input['color'],
            'owner_id' => $kanbanStatus->owner_id, //owner should not be updated
            'editors_ids' => array_merge($kanbanStatus->editors_ids, [auth()->user()->id])
        ]);

        if (request()->wantsJson()) {
            return $kanbanStatus;
        }
    }

    public function checkSync(Kanban $kanban)
    {
        $date = new DateTime;
        $date->modify('-10 seconds'); // refresh of max 10 seconds
        $formatted_date = $date->format('Y-m-d H:i:s');

        // check models for updated_at value (no need for created_at)
        $update_kanban = Kanban::select('id')
            ->where('id', $kanban->id)
            ->where('updated_at', '>=', $formatted_date)->get();
        $new_statuses = KanbanStatus::select('id')
            ->where('kanban_id', $kanban->id)
            ->where('updated_at', '>=', $formatted_date)->get();
        $new_items = KanbanItem::select('kanban_items.id')
            ->where('kanban_id', $kanban->id)
            ->leftJoin('kanban_item_comments', 'kanban_items.id', '=', 'kanban_item_comments.kanban_item_id')
            ->where(function ($query) use ($formatted_date) {
                $query->where('kanban_items.updated_at', '>=', $formatted_date)
                    ->orWhere('kanban_item_comments.updated_at', '>=', $formatted_date);
            })
            ->get();

        if (request()->wantsJson()) {
            if ($update_kanban->count() !== 0 OR $new_statuses->count() !== 0 OR $new_items->count() !== 0) {
                return ['message' => $kanban->withRelations()];
            } else {
                return ['message' => 'uptodate'];
            }
        }
    }

    public function sync(Request $request)
    {
        $this->validate(request(), [
            'statuses' => ['required', 'array'],
        ]);
        $kanban_id = KanbanStatus::select('kanban_id')->find($request->statuses[0]['id'])->kanban_id;
        abort_unless((\Gate::allows('kanban_show') and Kanban::find($kanban_id)->isAccessible()), 403);

        foreach ($request->statuses as $status) {
            $kanbanStatus = KanbanStatus::find($status['id']);
            $kanbanStatus->order_id = $status['order_id'];
            $kanbanStatus->save();
        }

        LogController::set(get_class($this).'@'.__FUNCTION__);
    }

    /**
     * @param KanbanStatus $kanbanStatus
     * @return array|void
     */
    public function destroy(KanbanStatus $kanbanStatus)
    {
        abort_unless((\Gate::allows('kanban_delete') and $kanbanStatus->isEditable(null, $this->getCurrentToken())), 403);

        $kanbanStatus->delete(); // further deletion logic handled in booted()-function
        Kanban::find($kanbanStatus->kanban_id)->touch('updated_at'); // to trigger delete-event for websockets
    }

    public function copyStatus(KanbanStatus $status, Request $request)
    {
        $order_id = KanbanStatus::where('kanban_id', $status->kanban_id)->max('order_id');

        $statusCopy = $status->replicate()->fill([
            'title'     => '[Kopie] ' . $status->title,
            'order_id'  => $order_id + 1,
            'owner_id'  => auth()->user()->id,
        ]);
        $statusCopy->save();

        foreach ($status->items as $item) {
            $itemCopy = $item->replicate()->fill([
                'kanban_id'         => $statusCopy->kanban_id,
                'kanban_status_id'  => $statusCopy->id,
                'editors_ids'       => [],
                'owner_id'          => auth()->user()->id,
            ]);
            $itemCopy->save();

            foreach ($item->mediaSubscriptions as $mediumSubscription) {
                $usage = null;
                // if Medium is external, we need to copy the usage
                if (!is_null($mediumSubscription->additional_data)) {
                    $usage = app(\App\Plugins\Repositories\edusharing\Edusharing::class)->createUsage(
                        'App\\KanbanItem',
                        $itemCopy->id,
                        $mediumSubscription->additional_data['nodeId'],
                        $mediumSubscription->medium()->pluck('owner_id')->first()
                    );
                }

                $mediumSubscription->replicate()->fill([
                    'subscribable_id'   => $itemCopy->id,
                    'owner_id'          => auth()->user()->id,
                    'additional_data'   => $usage,
                ])->save();
            }
        }

        Kanban::find($status['kanban_id'])->touch('updated_at'); // to trigger add-event for websockets

        return KanbanStatus::with([
                'items',
                'items.comments',
                'items.comments.user',
                'items.comments.likes',
                'items.likes',
                'items.mediaSubscriptions.medium',
                'items.owner:id,username,firstname,lastname',
            ])
            ->find($statusCopy->id);
    }

    protected function getCurrentToken()
    {
        $url = parse_url(request()->headers->get('referer'), PHP_URL_QUERY);
        parse_str($url ?? '', $query);
        return $query['sharing_token'] ?? null;
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
            'color' => 'sometimes',
        ]);
    }

    private function getStatusWithRelations(KanbanStatus $kanbanStatus): array
    {
        return [
            'message' => $kanbanStatus
                ->with([
                    'items' => function ($query) use ($kanbanStatus) {
                        $query->where('kanban_id', $kanbanStatus->kanban_id)
                              ->with(['owner', 'mediaSubscriptions.medium'])
                              ->orderBy('order_id');
                    },
                    'items.subscriptions',
                    'items.comments',
                    'items.comments.user',
                ])->where('id', $kanbanStatus->id)->get()->first()
        ];
    }
}

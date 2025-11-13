<?php

namespace App\Http\Controllers;

use App\Kanban;
use App\KanbanItem;
use App\KanbanStatus;
use App\Like;
use App\MediumSubscription;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class KanbanItemController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $input = $this->validateRequest();
        abort_unless(Gate::allows('kanban_edit') and Kanban::find($input['kanban_id'])->isEditable(null, $this->getCurrentToken()), 403);

        $order_id = DB::table('kanban_items')
            ->where('kanban_id', $input['kanban_id'])
            ->where('kanban_status_id', $input['kanban_status_id'])
            ->max('order_id');

        $kanbanItem = KanbanItem::create([
            'title'             => $input['title'],
            'description'       => $input['description'],
            'order_id'          => ($order_id === NULL) ? 0 : $order_id + 1,
            'kanban_id'         => $input['kanban_id'],
            'kanban_status_id'  => $input['kanban_status_id'],
            'color'             => $input['color'],
            'due_date'          => $input['due_date'],
            'locked'            => $input['locked'] ?? false,
            'editable'          => $input['editable'] ?? true,
            'replace_links'     => $input['replace_links'] ?? false,
            'visibility'        => $input['visibility'] ?? true,
            'visible_from'      => $input['visible_from'],
            'visible_until'     => $input['visible_until'],
            'owner_id'          => auth()->user()->id,
        ]);

        if (isset($input['media_subscriptions']) AND count($input['media_subscriptions']) > 0) {
            MediumSubscription::whereIn('medium_id', $input['media_subscriptions'])
            ->where([
                'subscribable_id'   => $input['kanban_status_id'],
                'subscribable_type' => 'App\\KanbanStatus',
            ])
            ->update([
                'subscribable_id'   => $kanbanItem->id,
                'subscribable_type' => 'App\\KanbanItem',
            ]);
        }

        LogController::set(get_class($this).'@'.__FUNCTION__);
        Kanban::find($input['kanban_id'])->touch('updated_at'); //To get Sync after media upload working

        if (request()->wantsJson()) {
            return KanbanItem::with([
                    'comments',
                    'comments.user',
                    'comments.likes',
                    'likes',
                    'mediaSubscriptions.medium',
                    'owner',
                ])
                    ->find($kanbanItem->id);
        }
    }

    public function sync(Request $request)
    {
        $this->validate(request(), [
            'items' => ['required', 'array'],
        ]);
        $kanban_id = KanbanItem::select('kanban_id')->find($request->items[0]['id'])->kanban_id;
        abort_unless((Gate::allows('kanban_show') and Kanban::find($kanban_id)->isAccessible()), 403);

        $kanbanStatusToTouch = [];
        foreach ($request->items as $item) {
            $kanbanItem = KanbanItem::find($item['id']);
            $kanbanItem->order_id = $item['order_id'];
            $kanbanItem->kanban_status_id = $item['kanban_status_id'];
            $kanbanItem->save();

            $kanbanStatusToTouch[] = $item['kanban_status_id'];
        }

        foreach ($kanbanStatusToTouch as $kanbanStatusToTouchId) {
            KanbanStatus::find($kanbanStatusToTouchId)->touch('updated_at'); //To get Sync after media upload working
        }

        LogController::set(get_class($this).'@'.__FUNCTION__);
    }

    /**
     * Display the specified resource.
     *
     * @param KanbanItem $kanbanItem
     * @return Response
     */
    public function show(KanbanItem $kanbanItem)
    {
        abort_unless((Gate::allows('kanban_show') and $kanbanItem->isAccessible()), 403);

        if (request()->wantsJson()) {
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

        return redirect()->action('KanbanController@show', ['kanban' => $kanbanItem->kanban_id]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request    $request
     * @param KanbanItem $kanbanItem
     * @return Response
     */
    public function update(Request $request, KanbanItem $kanbanItem)
    {
        abort_unless(Gate::allows('kanban_edit') and $kanbanItem->isEditable(null, $this->getCurrentToken()), 403);

        $input = $this->validateRequest();

        $kanbanItem->update([
            'title'             => $input['title'],
            'description'       => $input['description'],
            'kanban_id'         => $input['kanban_id'],
            'kanban_status_id'  => $input['kanban_status_id'],
            'color'             => $input['color'],
            'due_date'          => $input['due_date'],
            'locked'            => $input['locked'] ?? false,
            'editable'          => $input['editable'] ?? true,
            'replace_links'     => $input['replace_links'] ?? false,
            'visibility'        => $input['visibility'] ?? true,
            'visible_from'      => $input['visible_from'] ?? NULL,
            'visible_until'     => $input['visible_until'] ?? NULL,
            'owner_id'          => $kanbanItem->owner_id, //owner should not be updated
            'editors_ids'       => array_unique(array_merge($kanbanItem->editors_ids, [auth()->user()->id])),
        ]);

        if (request()->wantsJson()) {
            return KanbanItem::where('id', $kanbanItem->id)
                ->with([
                    'comments',
                    'comments.user',
                    'comments.likes',
                    'likes',
                    'mediaSubscriptions.medium',
                    'owner:id,username,firstname,lastname',
                ])
                ->get()->first();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param KanbanItem $kanbanItem
     * @return Response
     */
    public function destroy(KanbanItem $kanbanItem)
    {
        abort_unless(Gate::allows('kanban_delete') and $kanbanItem->isEditable(null, $this->getCurrentToken()), 403);

        $kanbanItem->delete(); // further deletion logic handled in booted()-function
    }

    public function copyItem(KanbanItem $item)
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
            'visibility'        => $item->visibility,
            'visible_from'      => $item->visible_from,
            'visible_until'     => $item->visible_until,
            'due_date'          => $item->due_date,
            'replace_links'     => $item->replace_links,
            'owner_id'          => auth()->user()->id,
        ]);

        foreach ($item->mediaSubscriptions as $mediaSubscription) {
            $usage = $mediaSubscription->additional_data;
            // if Medium is external, we need to create a seperate Usage for the new subscription
            if (!is_null($usage)) {
                app(\App\Interfaces\Implementations\EdusharingMediaAdapter::class)->createUsage(
                    $mediaSubscription->medium_id,
                    'App\\KanbanItem',
                    $itemCopy->id,
                    $usage["nodeId"]
                );
            } else { // local Media can simply be re-subscribed
                MediumSubscription::Create([
                    'medium_id'         => $mediaSubscription->medium_id,
                    'subscribable_id'   => $itemCopy->id,
                    'subscribable_type' => $mediaSubscription->subscribable_type,
                    'sharing_level_id'  => $mediaSubscription->sharing_level_id,
                    'visibility'        => $mediaSubscription->visibility,
                    'owner_id'          => auth()->user()->id,
                ]);
            }
        }

        KanbanStatus::find($item->kanban_status_id)->touch('updated_at'); //To get Sync after media upload working

        return KanbanItem::with([
                'comments',
                'comments.user',
                'comments.likes',
                'likes',
                'mediaSubscriptions.medium',
                'owner:id,username,firstname,lastname',
            ])
            ->find($itemCopy->id);
    }

    /**
     * React to kanbanItem the specified resource in storage.
     *
     * @param Request    $request
     * @param KanbanItem $kanbanItem
     * @return Response
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
            return [
                'user' => auth()->user()->only(['id', 'firstname', 'lastname']),
                'message' => KanbanItem::where('id', $kanbanItem->id)
                    ->with([
                        'likes',
                    ])->get()->first(),
            ];
        }
    }


    /**
     * Display the specified resource.
     *
     * @param KanbanItem $kanbanItem
     * @return Response
     */
    public function editors(KanbanItem $kanbanItem)
    {
        abort_unless(Gate::allows('kanban_show'), 403);

        if (request()->wantsJson()) {
            return $kanbanItem->editors(['id', 'username', 'firstname', 'lastname']);
        }
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
            'description' => 'sometimes',
            'order_id' => 'sometimes|required|integer',
            'kanban_id' => 'sometimes|required|integer',
            'kanban_status_id' => 'sometimes|required|integer',
            'media_subscriptions' => 'sometimes|array',
            'color' => 'sometimes',
            'due_date' => 'sometimes',
            'locked' => 'sometimes|boolean',
            'editable' => 'sometimes|boolean',
            'replace_links' => 'sometimes|boolean',
            'visibility' => 'sometimes|boolean',
            'visible_from' => 'sometimes',
            'visible_until' => 'sometimes',
            'editors_id' => 'sometimes',
        ]);
    }

}

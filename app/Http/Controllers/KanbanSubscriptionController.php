<?php

namespace App\Http\Controllers;

use App\Helpers\QRCodeHelper;
use App\Kanban;
use App\KanbanSubscription;
use App\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class KanbanSubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tokens = null;
        if (request()->wantsJson())
        {
            $tokenscodes = KanbanSubscription::where('kanban_id', request('kanban_id'))
                ->where('sharing_token', "!=", null)
                ->get();

            foreach ($tokenscodes as $token)
            {
                $tokens[] = [
                    "token" => $token,
                    "qr"    => (new QRCodeHelper())
                        ->generateQRCodeByString(
                            env("APP_URL"). "/kanbans/" . request('kanban_id') ."/token?sharing_token=" .$token->sharing_token
                        )
                ];
            }

            return [
                'tokens' => $tokens ?? [],
                'subscriptions' => optional(
                        optional(
                            Kanban::find(request('kanban_id'))
                        )->subscriptions()
                    )->with('subscribable')
                    ->whereHasMorph('subscribable', '*', function ($q, $type) {
                        if ($type == 'App\\User') {
                            $q->whereNot('id', env('GUEST_USER'));
                        }
                    })->get(),
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
        $kanban = Kanban::find(format_select_input($input['model_id']));
        abort_unless((\Gate::allows('kanban_create') and $kanban->isAccessible()), 403);

        $subscribe = KanbanSubscription::updateOrCreate([
            'kanban_id' => $kanban->id,
            'subscribable_type' => $input['subscribable_type'],
            'subscribable_id' => $input['subscribable_id'],
        ], [
            'editable' => isset($input['editable']) ? $input['editable'] : false,
            'owner_id' => auth()->user()->id,
        ]);
        $subscribe->save();

        if (request()->wantsJson()) {
            return $subscribe->with(['subscribable', 'kanban'])->find($subscribe->id);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\KanbanSubscription  $kanbanSubscription
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, KanbanSubscription $kanbanSubscription)
    {
        abort_unless((\Gate::allows('kanban_edit') and $kanbanSubscription->isAccessible()), 403);
        $input = $this->validateRequest();

        $kanbanSubscription->update([
            'editable' => isset($input['editable']) ? $input['editable'] : false,
            'owner_id' => auth()->user()->id,
        ]);

        if (request()->wantsJson()) {
            return ['editable' => $kanbanSubscription->editable];
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\KanbanSubscription  $kanbanSubscription
     * @return \Illuminate\Http\Response
     */
    public function destroy(KanbanSubscription $kanbanSubscription)
    {
        abort_unless((\Gate::allows('kanban_delete') and $kanbanSubscription->isAccessible()), 403);

        /** @var User $subscriber */
        $subscriber = $kanbanSubscription->subscribable;
        $is_user = get_class($subscriber) == User::class;
        if ($is_user && $subscriber->sharing_token != null) {
            $subscriber->delete();
        }
        $result = $kanbanSubscription->delete();

        if (request()->wantsJson()) {
            return ['message' => $result];
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function expel(Request $request)
    {
        $input = $this->validateRequest();
        $kanban = Kanban::find(format_select_input($input['model_id']));
        abort_unless((\Gate::allows('kanban_create') and $kanban->isAccessible()), 403);

        $subscription = KanbanSubscription::where([
            'kanban_id' => $kanban->id,
            'subscribable_type' => $input['subscribable_type'],
            'subscribable_id' => $input['subscribable_id'],
        ]);

        if ($subscription->delete()) {
            return trans('global.expel_success');
        }
    }

    protected function validateRequest()
    {
        return request()->validate([
            'subscribable_type' => 'sometimes|string',
            'subscribable_id'   => 'sometimes|integer',
            'model_id'          => 'sometimes',
            'editable'          => 'sometimes',
        ]);
    }
}

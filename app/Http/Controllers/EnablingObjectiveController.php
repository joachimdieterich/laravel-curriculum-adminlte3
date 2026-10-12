<?php

namespace App\Http\Controllers;

use App\Config;
use App\EnablingObjective;
use App\Group;
use App\QuoteSubscription;
use App\ReferenceSubscription;
use App\TerminalObjective;
use App\User;
use DB;
use Gate;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class EnablingObjectiveController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @return EnablingObjective|void
     */
    public function store()
    {
        $input = $this->validateRequest();
        abort_unless(TerminalObjective::find($input['terminal_objective_id'])->isAccessible(), 403);

        $order_id          = $this->getMaxOrderId($input['terminal_objective_id']);
        $enablingObjective = EnablingObjective::create([
            'title'                 => $input['title'],
            'description'           => $input['description'],
            'time_approach'         => $input['time_approach'],
            'curriculum_id'         => $input['curriculum_id'],
            'terminal_objective_id' => $input['terminal_objective_id'],
            'level_id'              => format_select_input($input['level_id']),
            'visibility'            => $input['visibility'],
            'order_id'              => $order_id,
        ]);

        LogController::set(get_class($this) . '@' . __FUNCTION__);

        if (request()->wantsJson()) {
            return EnablingObjective::with('achievements')->without('terminalObjective')->find($enablingObjective->id);
        }
    }

    /**
     * Display the specified resource.
     *
     * @return View
     */
    public function show(EnablingObjective $enablingObjective)
    {
        abort_unless($enablingObjective->isAccessible(), 403);

        $objective = EnablingObjective::with(
            [
                'curriculum:id,title,owner_id,subject_id',
                'curriculum.subject:id,title',
                'terminalObjective:id,title,description,color,curriculum_id,objective_type_id,visibility',
                'terminalObjective.type:id,title',
                'terminalObjective.enablingObjectives' => function ($query) {
                    $query->select('id', 'title', 'visibility', 'terminal_objective_id', 'level_id')
                        ->without('terminalObjective');
                },
                'variants',
                'variants.definition',
                'referenceSubscriptions.siblings.referenceable',
                'quoteSubscriptions.siblings.quotable',
                'achievements' => function ($query) {
                    $query->where('user_id', auth()->user()->id)->with(['owner', 'user']);
                },
            ])
            ->find($enablingObjective->id);

        $repository = Config::where('key', 'repository')->first() ?? 'false';
        $editable   = $objective->curriculum->isEditable();

        return view('objectives.show')
            ->with(compact('objective'))
            ->with(compact('repository'))
            ->with(compact('editable'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @return EnablingObjective|void
     */
    public function update(EnablingObjective $enablingObjective)
    {
        abort_unless($enablingObjective->isAccessible(), 403);

        $input = $this->validateRequest();

        if (request()->wantsJson()) {
            $enablingObjective->update([
                'title'                 => $input['title'],
                'description'           => $input['description'],
                'time_approach'         => $input['time_approach'],
                'curriculum_id'         => $input['curriculum_id'],
                'terminal_objective_id' => $input['terminal_objective_id'],
                'level_id'              => format_select_input($input['level_id']),
                'visibility'            => $input['visibility'],
            ]);

            return $enablingObjective->without(['terminalObjective', 'curriculum', 'owner'])->find($enablingObjective->id);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return bool|void|null
     */
    public function destroy(EnablingObjective $enablingObjective)
    {
        abort_unless((Gate::allows('objective_delete') and $enablingObjective->isAccessible()), 403);

        // delete contents
        foreach ($enablingObjective->contents as $content) {
            (new ContentController)->destroy($content, 'App\EnablingObjective', $enablingObjective->id); // delete or unsubscribe if content is still subscribed elsewhere
        }

        // decrease order-id of each objective with a higher order-id by 1
        EnablingObjective::where('terminal_objective_id', $enablingObjective->terminal_objective_id)
            ->where('order_id', '>', $enablingObjective->order_id)
            ->decrement('order_id');

        // delete objective
        $return = $enablingObjective->delete();

        if (request()->wantsJson()) {
            return $return;
        }
    }

    public function referenceSubscriptionSiblings(EnablingObjective $enablingObjective): array
    {
        abort_unless($enablingObjective->isAccessible(), 403);

        $siblings = new Collection([]);

        foreach ($enablingObjective->referenceSubscriptions as $referenceSubscription) {
            $collection = ReferenceSubscription::where('reference_id', '=', $referenceSubscription->reference_id)
                ->where(function ($query) use ($referenceSubscription, $enablingObjective) {
                    return $query->where('reference_id', '=', $referenceSubscription->reference_id)
                        ->where('referenceable_id', '!=', $enablingObjective->id);
                })
                ->with(['referenceable.curriculum.organizationType'])
                ->with(['reference'])
                ->get();
            $siblings = $siblings->merge($collection);
        }

        if (count($siblings) == 0) { // end early
            return ['message' => 'no subscriptions'];
        }

        $curricula_list = [];
        foreach ($siblings as $sibling) {
            $curricula_list[$sibling->referenceable->curriculum->id] = $sibling->referenceable->curriculum;
        }

        return ['siblings' => $siblings, 'curricula_list' => $curricula_list];
    }

    public function quoteSubscriptions(EnablingObjective $enablingObjective): array
    {
        abort_unless($enablingObjective->isAccessible(), 403);

        $collection = QuoteSubscription::where('quotable_id', '=', $enablingObjective->id)
            ->where('quotable_type', '=', 'App\EnablingObjective')
            ->with(['quote.content.subscriptions.subscribable'])
            ->get();

        if (count($collection) == 0) { // end early
            return ['message' => 'no subscriptions'];
        }

        $quotes_subscriptions = [];
        $curricula_list       = [];

        foreach ($collection as $quote_subscriptions) {
            if (! is_null($quote_subscriptions->quote)) {
                $id                     = $quote_subscriptions->quote->content->subscriptions[0]->subscribable->id;
                $curricula_list[$id]    = optional($quote_subscriptions->quote->content)->subscriptions[0]->subscribable;
                $quotes_subscriptions[] = $quote_subscriptions;
            }
        }

        return ['quotes_subscriptions' => $quotes_subscriptions, 'curricula_list' => $curricula_list];
    }

    protected function getMaxOrderId($terminal_objective_id)
    {
        abort_unless(TerminalObjective::find($terminal_objective_id)->isAccessible(), 403);

        $order_id = DB::table('enabling_objectives')
            ->where('terminal_objective_id', $terminal_objective_id)
            ->max('order_id');

        return (is_numeric($order_id)) ? $order_id + 1 : 0;
    }

    public function higher(EnablingObjective $enablingObjective)
    {
        abort_unless($enablingObjective->isAccessible(), 403);

        // decrease order_id of the objective with the next highest order_id
        EnablingObjective::where([
            'terminal_objective_id' => $enablingObjective->terminal_objective_id,
            'order_id'              => $enablingObjective->order_id + 1,
        ])->decrement('order_id');

        $enablingObjective->order_id++;
        $enablingObjective->save();

        return EnablingObjective::where('terminal_objective_id', $enablingObjective->terminal_objective_id)->without('terminalObjective')->orderBy('order_id')->get();
    }

    public function lower(EnablingObjective $enablingObjective)
    {
        abort_unless($enablingObjective->isAccessible(), 403);

        // increase order_id of the objective with the next lowest order_id
        EnablingObjective::where([
            'terminal_objective_id' => $enablingObjective->terminal_objective_id,
            'order_id'              => $enablingObjective->order_id - 1,
        ])->increment('order_id');

        $enablingObjective->order_id--;
        $enablingObjective->save();

        return EnablingObjective::where('terminal_objective_id', $enablingObjective->terminal_objective_id)->without('terminalObjective')->orderBy('order_id')->get();
    }

    /**
     * Display the specified resource with achievements.
     *
     * @return array|void
     */
    public function showAchievements(EnablingObjective $enablingObjective, $group = null)
    {
        abort_unless($enablingObjective->isAccessible(), 403);

        $user_ids = [auth()->user()->id];
        // check if user is allowed to see group
        if (is_admin() || auth()->user()->groups->contains($group)) {
            $user_ids = Group::find($group)->users()->get()->pluck('id');
        }

        $result = [
            'objective' => EnablingObjective::with([
                'achievements' => function ($query) use ($user_ids) {
                    $query->whereIn('user_id', $user_ids)->with(['owner', 'user']);
                },
            ])->find($enablingObjective->id),
            'users' => User::select([
                'users.id',
                'firstname',
                'lastname',
            ])
                ->join('group_user', 'users.id', '=', 'group_user.user_id')
                ->join('organization_role_users', 'organization_role_users.user_id', '=', 'group_user.user_id')
                ->where('group_user.group_id', '=', $group)
                ->where('organization_role_users.organization_id', '=', auth()->user()->current_organization_id)
                ->where('organization_role_users.role_id', '=', 6) // 6 == student
                ->get(),
            'groups' => auth()->user()->currentGroupEnrolments()->where('curriculum_id', $enablingObjective->curriculum_id)->get(),
        ];

        if (request()->wantsJson()) {
            return $result;
        }
    }

    protected function validateRequest(): array
    {
        return request()->validate([
            'id'                    => 'sometimes',
            'title'                 => 'sometimes',
            'description'           => 'sometimes',
            'time_approach'         => 'sometimes',
            'curriculum_id'         => 'sometimes',
            'terminal_objective_id' => 'sometimes',
            'level_id'              => 'sometimes',
            'visibility'            => 'sometimes',
        ]);
    }
}

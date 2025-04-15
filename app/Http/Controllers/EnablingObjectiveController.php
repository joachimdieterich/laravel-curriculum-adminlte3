<?php

namespace App\Http\Controllers;

use App\Config;
use App\Curriculum;
use App\EnablingObjective;
use App\Group;
use App\Medium;
use App\QuoteSubscription;
use App\ReferenceSubscription;
use App\TerminalObjective;
use App\User;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class EnablingObjectiveController extends Controller
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
        abort_unless(TerminalObjective::find($input['terminal_objective_id'])->isAccessible(), 403);

        $order_id = $this->getMaxOrderId($input['terminal_objective_id']);
        $enablingObjective = EnablingObjective::create([
            'title'                 => $input['title'],
            'description'           => $input['description'],
            'time_approach'         => $input['time_approach'],
            'curriculum_id'         => $input['curriculum_id'],
            'terminal_objective_id' => $input['terminal_objective_id'],
            'level_id'              => format_select_input($input['level_id']),
            'visibility'            => $input['visibility'],
            'order_id'              => $order_id
        ]);

        LogController::set(get_class($this).'@'.__FUNCTION__);

        if (request()->wantsJson()) {
            return EnablingObjective::with('achievements')->without('terminalObjective')->find($enablingObjective->id);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\EnablingObjective  $enablingObjective
     * @return \Illuminate\Http\Response
     */
    public function show(EnablingObjective $enablingObjective)
    {
        abort_unless($enablingObjective->isAccessible(), 403);

        $objective = EnablingObjective::where('id', $enablingObjective->id)
            ->with(['curriculum', 'curriculum.subject', 'terminalObjective.type', 'variants', 'variants.definition',
                'referenceSubscriptions.siblings.referenceable', 'quoteSubscriptions.siblings.quotable',
                'achievements' => function ($query) {
                    $query->where('user_id', auth()->user()->id)->with(['owner', 'user']);
                },
            ])
            ->get()->first();

        $repository = Config::where('key', 'repository')->get()->first() ?? 'false';
        $editable = $objective->curriculum->isEditable();

        return view('objectives.show')
            ->with(compact('objective'))
            ->with(compact('repository'))
            ->with(compact('editable'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\EnablingObjective  $enablingObjective
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EnablingObjective $enablingObjective)
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
                'visibility'            => $input['visibility']
            ]);
            return $enablingObjective->without(['terminalObjective', 'curriculum', 'owner'])->find($enablingObjective->id);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\EnablingObjective  $enablingObjective
     * @return \Illuminate\Http\Response
     */
    public function destroy(EnablingObjective $enablingObjective)
    {
        abort_unless((\Gate::allows('objective_delete') and $enablingObjective->isAccessible()), 403);

        // delete contents
        foreach ($enablingObjective->contents as $content) {
            (new ContentController)->destroy($content, 'App\EnablingObjective', $enablingObjective->id); // delete or unsubscribe if content is still subscribed elsewhere
        }
        
        // decrease order-id of each objective with a higher order-id by 1
        EnablingObjective::where('terminal_objective_id', $enablingObjective->terminal_objective_id)
            ->where('order_id', '>', $enablingObjective->order_id)
            ->decrement('order_id', 1);

        // delete objective
        $return = $enablingObjective->delete();

        if (request()->wantsJson()) {
            return $return;
        }
    }

    public function referenceSubscriptionSiblings(EnablingObjective $enablingObjective)
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

        if (count($siblings) == 0) { //end early
            return ['message'=> 'no subscriptions'];
        }

        foreach ($siblings as $sibling) {

            //todo: trace error -> "Trying to get property 'curriculum' of non-object": possible sibling entry of deleted curriculum? -> yes.
            //todo: add artisan command/or frontend cleaner
            $curricula_list[$sibling->referenceable->curriculum->id] = $sibling->referenceable->curriculum;
        }

        return ['siblings' => $siblings, 'curricula_list' => $curricula_list];
    }

    public function quoteSubscriptions(EnablingObjective $enablingObjective)
    {
        abort_unless($enablingObjective->isAccessible(), 403);

        $collection = QuoteSubscription::where('quotable_id', '=', $enablingObjective->id)
            ->where('quotable_type', '=', 'App\EnablingObjective')
            ->with(['quote.content.subscriptions.subscribable'])
            ->get();

        if (count($collection) == 0) { //end early
            return ['message' => 'no subscriptions'];
        }

        foreach ($collection as $quote_subscriptions) {
            $arr[$quote_subscriptions->quote_id] = ! is_null($quote_subscriptions->quote);

            if (! is_null($quote_subscriptions->quote)) {
                $curricula_list[$quote_subscriptions->quote->content->subscriptions[0]->subscribable->id] = optional($quote_subscriptions->quote->content)->subscriptions[0]->subscribable;
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
            'order_id' => $enablingObjective->order_id + 1,
        ])->decrement('order_id', 1);

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
            'order_id' => $enablingObjective->order_id - 1,
        ])->increment('order_id', 1);

        $enablingObjective->order_id--;
        $enablingObjective->save();

        return EnablingObjective::where('terminal_objective_id', $enablingObjective->terminal_objective_id)->without('terminalObjective')->orderBy('order_id')->get();
        // return TerminalObjective::find($enablingObjective->terminal_objective_id)->enablingObjectives()->get();
    }

    /**
     * Display the specified resource with achievements.
     *
     * @param  \App\EnablingObjective  $enablingObjective
     * @return array
     */
    public function showAchievements(EnablingObjective $enablingObjective, $group = null)
    {
        abort_unless($enablingObjective->isAccessible(), 403);

        if ($group == 'null') {
            $user_ids = [auth()->user()->id];
        } else {
            if (auth()->user()->groups->contains($group) //check if user is allowed to see group
                or is_admin()) {
                $user_ids = Group::find($group)->users()->get()->pluck('id');
            } else {
                $user_ids = [auth()->user()->id];
            }
        }

        $result = ['objective' => EnablingObjective::with(
                ['achievements' => function ($query) use ($user_ids) {
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
                ->where('organization_role_users.role_id', '=', 6) //6 == student
                ->get(),
            'groups' => auth()->user()->currentGroupEnrolments()->where('curriculum_id', $enablingObjective->curriculum_id)->get(),
        ];

        if (request()->wantsJson()) {
            return $result;
        }
    }

    protected function validateRequest()
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

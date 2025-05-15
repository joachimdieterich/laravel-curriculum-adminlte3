<?php

namespace App\Http\Controllers;

use App\Plan;
use App\PlanSubscription;
use App\CurriculumSubscription;
use App\TerminalObjective;
use App\EnablingObjective;
use Illuminate\Http\Request;

class PlanSubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_unless(\Gate::allows('plan_create'), 403);
        if (request()->wantsJson()) {
            return [
                'subscriptions' => Plan::find(request('plan_id'))->subscriptions()->with('subscribable')->get(),
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
        $plan = Plan::find(format_select_input($input['model_id']));
        abort_unless((\Gate::allows('plan_create') and $plan->isAccessible()), 403);   // user owns plan_subscription

        $subscribe = PlanSubscription::updateOrCreate([
            'plan_id' => $plan->id,
            'subscribable_type' => $input['subscribable_type'],
            'subscribable_id' => $input['subscribable_id'],
        ], [
            'editable' => isset($input['editable']) ? $input['editable'] : false,
            'owner_id' => auth()->user()->id,
        ]);
        $subscribe->save();

        // if group-subscription, connect Curricula used in Plan to the group
        if ($input['subscribable_type'] == "App\Group") {
            $entry_ids = $plan->entries()->pluck('id');
            // get connected Curricula-IDs through its terminal-/enabling-subscriptions
            $terminal = TerminalObjective::join('terminal_objective_subscriptions AS sub', 'terminal_objectives.id', '=', 'sub.terminal_objective_id')
                ->where('subscribable_type', 'App\\PlanEntry')
                ->whereIn('subscribable_id', $entry_ids)
                ->pluck('curriculum_id');

            $enabling = EnablingObjective::join('enabling_objective_subscriptions AS sub', 'enabling_objectives.id', '=', 'sub.enabling_objective_id')
                ->where('subscribable_type', 'App\\PlanEntry')
                ->whereIn('subscribable_id', $entry_ids)
                ->pluck('curriculum_id');
            // remove duplicates and keys
            $curriculum_ids = $terminal->merge($enabling)->unique()->flatten();

            // subscribe those Curricula to the group if it isn't already
            foreach ($curriculum_ids as $id) {
                CurriculumSubscription::firstOrCreate([
                    'curriculum_id' => $id,
                    'subscribable_type' => 'App\\Group',
                    'subscribable_id' => $input['subscribable_id'],
                    'owner_id' => auth()->user()->id,
                ]);
            }
        }

        if (request()->wantsJson()) {
            return $subscribe->with(['subscribable', 'plan'])->find($subscribe->id);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\PlanSubscription  $planSubscription
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PlanSubscription $planSubscription)
    {
        abort_unless((\Gate::allows('plan_edit') and $planSubscription->isAccessible()), 403);

        $input = $this->validateRequest();
        $planSubscription->update([
            'editable' => isset($input['editable']) ? $input['editable'] : false,
            'owner_id' => auth()->user()->id,
        ]);

        if (request()->wantsJson()) {
            return ['editable' => $planSubscription->editable];
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\PlanSubscription  $planSubscription
     * @return \Illuminate\Http\Response
     */
    public function destroy(PlanSubscription $planSubscription)
    {
        abort_unless((\Gate::allows('plan_delete') and $planSubscription->isAccessible()), 403);

        if (request()->wantsJson()) {
            return ['message' => $planSubscription->delete()];
        }
    }

    public function expel(Request $request) {
        $input = $this->validateRequest();
        $plan = Plan::find(format_select_input($input['model_id']));
        abort_unless((\Gate::allows('plan_delete') and $plan->isAccessible()), 403);

        $subscription = PlanSubscription::where([
            'plan_id' => $plan->id,
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

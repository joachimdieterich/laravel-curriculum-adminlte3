<?php

namespace App\Http\Controllers;

use App\EnablingObjective;
use App\EnablingObjectiveSubscriptions;
use App\TerminalObjective;
use App\Plan;
use Illuminate\Http\Request;

class EnablingObjectiveSubscriptionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $input = $this->validateRequest();
        if (!isset($input['subscribable_type']) or !isset($input['subscribable_id'])) return;

        $objective = $input['subscribable_type']::find($input['subscribable_id']);
        abort_unless((\Gate::allows('curriculum_show') and $objective->isAccessible()), 403);

        $user_ids = [];

        if ($input['subscribable_type'] == 'App\PlanEntry' and $objective->plan->isEditable()) {
            $user_ids = array_column(app(PlanController::class)->getUsers(Plan::find($objective->plan->id)), 'id');
        } else {
            $user_ids = [auth()->user()->id];
        }

        $terminal_ids = array_values(
            EnablingObjective::join('enabling_objective_subscriptions', 'enabling_objectives.id', '=', 'enabling_objective_subscriptions.enabling_objective_id')
                ->where('subscribable_type', $input['subscribable_type'])
                ->where('subscribable_id', $input['subscribable_id'])
                ->without(['terminalObjective', 'level'])
                ->distinct()
                ->pluck('terminal_objective_id')
                ->toArray()
        );

        if (request()->wantsJson()) {
            return TerminalObjective::select('id', 'title', 'description', 'color', 'curriculum_id')
                ->whereIn('id', $terminal_ids)
                ->with(['enablingObjectives' => function($query) use ($input, $user_ids) {
                    $query->select('id', 'title', 'description', 'terminal_objective_id')
                        ->without(['terminalObjective', 'level'])
                        ->join('enabling_objective_subscriptions', 'enabling_objectives.id', '=', 'enabling_objective_subscriptions.enabling_objective_id')
                        ->where('subscribable_type', $input['subscribable_type'])
                        ->where('subscribable_id', $input['subscribable_id'])
                        ->with(['achievements' => function($query) use ($user_ids) {
                            $query->whereIn('user_id', $user_ids)
                            ->with([
                                'owner' => function($query) {
                                    $query->select('id', 'firstname', 'lastname');
                                },
                                'user' => function($query) {
                                    $query->select('id', 'firstname', 'lastname');
                                },
                            ]);
                        }])
                        ->orderBy('order_id')
                        ->get();
                }])
                ->get();
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     */
    public function store(Request $request)
    {
        $input = $this->validateRequest();
        abort_unless($input['subscribable_type']::find($input['subscribable_id'])->isAccessible(), 403);

        $new_subscriptions = [];
        $objectives = EnablingObjective::find($input['enabling_objective_id']);

        foreach ($objectives as $objective) {
            abort_unless($objective->isAccessible(), 403);
            array_push($new_subscriptions, [
                'enabling_objective_id' => $objective->id,
                'subscribable_type' => $input['subscribable_type'],
                'subscribable_id' => $input['subscribable_id'],
                'sharing_level_id' => 1,
                'visibility' => true,
                'owner_id' => auth()->user()->id,
                'created_at' => now(), // insertOrIgnore does not create timestamps
                'updated_at' => now(),
            ]);
        }
        // insertOrIgnore is used to prevent duplicates
        EnablingObjectiveSubscriptions::insertOrIgnore($new_subscriptions);

        if (request()->wantsJson()) {
            return TerminalObjective::select('id', 'title', 'description', 'color', 'curriculum_id')
                ->whereIn('id', $input['terminal_objective_id'])
                ->with(['enablingObjectives' => function ($query) use ($input) {
                    // inside 'with' the 'select'-statement needs to include the foreign key, or else it'll return '0'
                    $query->select('id', 'title', 'description', 'terminal_objective_id')
                        ->without(['terminalObjective', 'level'])
                        // query enabling objectives again, incase a input existed before
                        ->join('enabling_objective_subscriptions', 'enabling_objectives.id', '=', 'enabling_objective_subscriptions.enabling_objective_id')
                        ->where('subscribable_type', $input['subscribable_type'])
                        ->where('subscribable_id', $input['subscribable_id'])
                        ->orderBy('order_id')
                        ->get();
                }])->get();
        }
    }

    public function destroy(Request $request)
    {
        $input = $this->validateRequest();
        abort_unless(is_admin() or $input['subscribable_type']::find($input['subscribable_id'])->isAccessible(), 403);

        return EnablingObjectiveSubscriptions::whereIn('enabling_objective_id', $input['enabling_objective_id'])
            ->where([
                'subscribable_type' => $input['subscribable_type'],
                'subscribable_id' => $input['subscribable_id'],
            ])->delete();
    }

    protected function validateRequest()
    {
        return request()->validate([
            'enabling_objective_id' => 'sometimes|required',
            'terminal_objective_id' => 'sometimes',
            'subscribable_type' => 'required',
            'subscribable_id' => 'required',
            'sharing_level_id' => 'sometimes',
            'visibility' => 'sometimes',
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\TerminalObjective;
use App\TerminalObjectiveSubscriptions;
use App\Plan;
use Illuminate\Http\Request;

class TerminalObjectiveSubscriptionsController extends Controller
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

        $model = $input['subscribable_type']::find($input['subscribable_id']);
        abort_unless((\Gate::allows('curriculum_show') and $model->isAccessible()), 403);

        $user_ids = [];

        if ($input['subscribable_type'] == 'App\PlanEntry' and $model->plan->isEditable()) {
            $user_ids = array_column(app(PlanController::class)->getUsers(Plan::find($model->plan->id)), 'id');
        } else {
            $user_ids = [auth()->user()->id];
        }

        if (request()->wantsJson()) {
            return TerminalObjective::select('id', 'title', 'description', 'color', 'curriculum_id', 'terminal_objectives.visibility')
                ->join('terminal_objective_subscriptions', 'terminal_objectives.id', '=', 'terminal_objective_subscriptions.terminal_objective_id')
                ->where('subscribable_type', $input['subscribable_type'])
                ->where('subscribable_id', $input['subscribable_id'])
                ->with(['enablingObjectives' => function($query) use ($user_ids) {
                    $query->select('id', 'title', 'description', 'terminal_objective_id', 'visibility')
                        ->without(['terminalObjective', 'level'])
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
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $this->validateRequest();
        $model = $input['subscribable_type']::find($input['subscribable_id']);
        abort_unless($model->isAccessible(), 403, 'Model <' . $input['subscribable_type'] . ':' . $input['subscribable_id'] . '> not accessible!');

        $new_subscriptions = [];
        $objectives = TerminalObjective::find($input['terminal_objective_id']);

        foreach ($objectives as $objective) {
            abort_unless($objective->isAccessible(), 403, 'TerminalObjective:' . $objective->id . ' not accessible!');
            array_push($new_subscriptions, [
                'terminal_objective_id' => $objective->id,
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
        TerminalObjectiveSubscriptions::insertOrIgnore($new_subscriptions);
        
        // when adding objectives to a PlanEntry, check if all subscribed groups are subscribed to the curriculum
        if ($model instanceof \App\PlanEntry) {
            $groups = $model->plan->groupSubscriptions()->pluck('subscribable_id')->toArray();

            foreach ($groups as $group_id) {
                \App\CurriculumSubscription::firstOrCreate([
                    'curriculum_id' => $objectives->first()->curriculum_id, // curriculum_id is the same for all objectives
                    'subscribable_type' => 'App\\Group',
                    'subscribable_id' => $group_id,
                ],[
                    'owner_id' => auth()->user()->id,
                ]);
            }
        }

        if (request()->wantsJson()) {
            return TerminalObjective::select('id', 'title', 'description', 'color', 'curriculum_id', 'visibility')
                ->with(['enablingObjectives' => function ($query) use ($input) {
                    // inside 'with' the 'select'-statement needs to include the foreign key, or else it'll return '0'
                    $query->select('id', 'title', 'description', 'visibility', 'terminal_objective_id')
                        ->without(['terminalObjective', 'level'])
                        ->with(['achievements' => function($query) use ($input) {
                            $query->whereIn('user_id', $input['users']);
                        }])
                        ->orderBy('order_id');
                }])
                ->find($input['terminal_objective_id']);
        }
    }

    public function destroySubscription(Request $request)
    {
        $input = $this->validateRequest();
        abort_unless(is_admin() or $input['subscribable_type']::find($input['subscribable_id'])->isAccessible(), 403);

        return TerminalObjectiveSubscriptions::where([
            'terminal_objective_id' => $input['terminal_objective_id'],
            'subscribable_type' => $input['subscribable_type'],
            'subscribable_id' => $input['subscribable_id'],
        ])->delete();
    }

    protected function validateRequest()
    {
        return request()->validate([
            'terminal_objective_id' => 'sometimes|required',
            'subscribable_type' => 'required',
            'subscribable_id' => 'required',
            'sharing_level_id' => 'sometimes',
            'visibility' => 'sometimes',
            'users' => 'sometimes|array',
        ]);
    }
}

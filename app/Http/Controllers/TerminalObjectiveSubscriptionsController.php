<?php

namespace App\Http\Controllers;

use App\TerminalObjective;
use App\TerminalObjectiveSubscriptions;
use App\User;
use App\Group;
use App\Organization;
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
        if (isset($input['subscribable_type']) and isset($input['subscribable_id'])) {
            $modal = $input['subscribable_type']::find($input['subscribable_id']);
            abort_unless((\Gate::allows('curriculum_show') and $modal->isAccessible()), 403);

            $user_ids = [];
            
            if ($input['subscribable_type'] == 'App\PlanEntry' and $modal->plan->isEditable()) {
                $subscriptions = $modal->plan->subscriptions;

                // get every user-id through all subscriptions
                foreach ($subscriptions as $subscription) {
                    switch ($subscription['subscribable_type']) {
                        case 'App\User':
                            array_push($user_ids, User::find($subscription['subscribable_id'])->id);
                            break;
                        case 'App\Group':
                            $user_ids = array_merge($user_ids, Group::find($subscription['subscribable_id'])->users()->get()->pluck('id')->toArray());
                            break;
                        case 'App\Organization':
                            $user_ids = array_merge($user_ids, Organization::find($subscription['subscribable_id'])->users()->get()->pluck('id')->toArray());
                            break;
                        default:
                            break;
                    }
                }

                // duplicates have to be removed, because SQL will return the same entry multiple times
                $user_ids = array_unique($user_ids, SORT_NUMERIC);
            } else {
                $user_ids = [auth()->user()->id];
            }

            if (request()->wantsJson()) {
                return [
                    'subscriptions' =>
                        TerminalObjectiveSubscriptions::where('subscribable_type', $input['subscribable_type'])
                            ->where('subscribable_id', $input['subscribable_id'])
                            ->with([
                                'terminalObjective',
                                // 'terminalObjective.achievements', // there's currently no implementation for this
                                'terminalObjective.enablingObjectives',
                                'terminalObjective.enablingObjectives.achievements' => function ($query) use ($user_ids) {
                                    $query->whereIn('user_id', $user_ids)->with(['owner', 'user']);
                                },
                            ])
                            ->get()
                ];
            }
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
        $new_subscription = $this->validateRequest();

        $model = TerminalObjective::find($new_subscription['terminal_objective_id']);
        abort_unless($model->isAccessible(), 403);

        $subscription = TerminalObjectiveSubscriptions::firstOrCreate([
            'terminal_objective_id' => $new_subscription['terminal_objective_id'],
            'subscribable_type' => $new_subscription['subscribable_type'],
            'subscribable_id' => $new_subscription['subscribable_id'],
            'sharing_level_id' => 1,
            'visibility' => true,
            'owner_id' => auth()->user()->id,
        ]);
        if (request()->wantsJson()) {
            return ['message' => 'ok'];
        }
    }

    public function destroySubscription(Request $request)
    {
        $subscription = $this->validateRequest();

        return TerminalObjectiveSubscriptions::where([
            'terminal_objective_id' => $subscription['terminal_objective_id'],
            'subscribable_type' => $subscription['subscribable_type'],
            'subscribable_id' => $subscription['subscribable_id'],
            'sharing_level_id' => $subscription['sharing_level_id'],
            'visibility' => $subscription['visibility'],
            //"owner_id"=> auth()->user()->id, //Todo: admin should be able to delete everything
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
        ]);
    }
}

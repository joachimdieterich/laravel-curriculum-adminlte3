<?php

namespace App\Http\Controllers;

use App\Organization;
use App\Plan;
use App\User;
use App\Group;
use App\PlanEntry;
use App\TerminalObjectiveSubscriptions;
use App\EnablingObjectiveSubscriptions;
use App\Training;
use App\TrainingSubscription;
use App\Exercise;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class PlanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_unless(\Gate::allows('plan_access'), 403);
        if (request()->wantsJson())
        {
            return getEntriesForSelect2ByCollection(
                $this->getPlans(),
                'plans.'
            );
        }
        else
        {
            return view('plans.index');
        }
    }

    public function getPlans($withOwned = true)
    {
        $plans = Plan::with('subscriptions')
            ->whereHas('subscriptions', function ($query) {
                $query->where(
                    function ($query) {
                        $query->where('subscribable_type', 'App\\Organization')->where('subscribable_id', auth()->user()->current_organization_id);
                    }
                )->orWhere(
                    function ($query) {
                        $query->where('subscribable_type', 'App\\Group')->whereIn('subscribable_id', auth()->user()->groups->pluck('id'));
                    }
                )->orWhere(
                    function ($query) {
                        $query->where('subscribable_type', 'App\\User')->where('subscribable_id', auth()->user()->id);
                    }
                )->orWhere(
                    function ($query) {
                        $query->where('subscribable_type', 'App\\User')->where('subscribable_id', auth()->user()->id);
                    }
                );
            })->orWhere('owner_id', auth()->user()->id);

        if ($withOwned) {
            $plans = $plans->orWhere('owner_id', auth()->user()->id);
        }

        return $plans;
    }

    protected function userPlans($withOwned = true)
    {
        $userCanSee = auth()->user()->plans;

        foreach (auth()->user()->currentGroups as $group) {
            $userCanSee = $userCanSee->merge($group->plans);
        }

        $organization = Organization::find(auth()->user()->current_organization_id)->plans;
        $userCanSee = $userCanSee->merge($organization);

        if ($withOwned)
        {
            $owned = Plan::where('owner_id', auth()->user()->id)->get();
            $userCanSee = $userCanSee->merge($owned);

        }

        return $userCanSee->unique();
    }

    public function list()
    {
        abort_unless(\Gate::allows('plan_access'), 403);
        $plans = (auth()->user()->role()->id == 1) ? Plan::all() : $this->userPlans();

        return DataTables::of($plans)
            ->setRowId('id')
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort( 403);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        abort_unless(\Gate::allows('plan_create'), 403);

        $input = $this->validateRequest();
        $plan = Plan::firstOrCreate([
            'title'             => $input['title'],
            'description'       => $input['description'],
            'begin'             => $input['begin'],
            'end'               => $input['end'],
            'duration'          => $input['duration'],
            'type_id'           => format_select_input($input['type_id']),
            'owner_id'          => auth()->user()->id,
        ]);

        checkForEmbeddedMedia($plan, 'description'); // subscribe embedded media

        if (request()->wantsJson()) {
            return $plan;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Plan  $plan
     * @return \Illuminate\Http\Response
     */
    public function show(Plan $plan)
    {
        abort_unless((\Gate::allows('plan_show') and $plan->isAccessible()), 403);
        $editable = $plan->isEditable();
        $users = [];

        if ($editable) {
            $subscriptions = $plan->subscriptions()->get()->toArray();
            // get every user-id through all subscriptions
            foreach ($subscriptions as $subscription) {
                switch ($subscription['subscribable_type']) {
                    case 'App\User':
                        array_push($users, $subscription['subscribable_id']);
                        break;
                    case 'App\Group':
                        $ids = Group::find($subscription['subscribable_id'])->users()->get()->pluck('id')->toArray();
                        $users = array_merge($users, $ids);
                        break;
                    case 'App\Organization':
                        $ids = Organization::find($subscription['subscribable_id'])->users()->get()->pluck('id')->toArray();
                        $users = array_merge($users, $ids);
                        break;
                    default:
                        break;
                }
            }

            $users = array_unique($users, SORT_NUMERIC); // duplicates have to be removed, because SQL will return the same entry multiple times

            // get needed user-data through their ID
            $users = User::select('id', 'firstname', 'lastname')->whereIn('id', $users)->get()->toArray();
        }

        if (request()->wantsJson()) {
            return [
                'plan' => $plan,
                'users' => $users,
                'editable' => $editable,
            ];
        }

        return view('plans.show')
            ->with(compact('plan', 'users', 'editable'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Plan  $plan
     * @return \Illuminate\Http\Response
     */
    public function edit(Plan $plan)
    {
        abort( 403);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Plan  $plan
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Plan $plan)
    {
        abort_unless((\Gate::allows('plan_edit') and $plan->isAccessible()), 403);
        $clean_data = $this->validateRequest();
        if (isset($clean_data['type_id'])) {
            $clean_data['type_id'] = format_select_input($clean_data['type_id']); //hack to prevent array to string conversion
        }

        $plan->update($clean_data);

        checkForEmbeddedMedia($plan, 'description');// subscribe embedded media

        if (request()->wantsJson()) {
            return $plan;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Plan  $plan
     * @return \Illuminate\Http\Response
     */
    public function destroy(Plan $plan)
    {
        abort_unless((
            \Gate::allows('plan_delete') and
            ($plan->owner->id == auth()->user()->id or is_admin())
        ), 403);

        // objectivesSubscriptions aren't automatically removed
        foreach ($plan->entries as $entry) {
            $entry->enablingObjectiveSubscriptions()->delete();
            $entry->terminalObjectiveSubscriptions()->delete();
            
            // trainings need to be deleted separately
            foreach ($entry->trainings as $training) {
                $training->exercises()->delete();
                (new TrainingController())->destroy($training);
            }
        }

        $plan->entries()->delete();
        $plan->subscriptions()->delete();
        //? if media-subscriptions can be added in the future, they need to be deleted too
        $plan->delete();
    }

    public function syncEntriesOrder(Plan $plan)
    {
        abort_unless(auth()->user()->id == $plan->owner_id, 403);

        $input = $this->validateRequest();

        $plan->update([
            'entry_order' => $input['entry_order']
        ]);

        return ['entry_order' => $plan->entry_order];
    }

    public function copyPlan(Plan $plan) {
        $ownerId = auth()->user()->id;
        $assocOrder = [];

        $planCopy = Plan::create([
            'title' => $plan->title . '_' . date('Y.m.d_H:i:s'),
            'description' => $plan->description,
            'color' => $plan->color,
            'begin' => $plan->begin,
            'end' => $plan->end,
            'duration' => $plan->duration,
            'type_id' => $plan->type_id,
            // 'medium_id' => $plan->medium_id,
            'allow_copy' => $plan->allow_copy,
            'entry_order' => null, // entry_order needs to be set after creating new PlanEntries
            'owner_id' => $ownerId,
        ]);

        foreach ($plan->entries as $entry) {
            $entryCopy = PlanEntry::Create([
                'title' => $entry->title,
                'description' => $entry->description,
                'css_icon' => $entry->css_icon,
                'color' => $entry->color,
                'medium_id' => $entry->medium_id,
                'order_id' => $entry->order_id,
                'plan_id' => $planCopy->id,
                'owner_id' => $ownerId,
            ]);
            // associative array, where old entry-id shows the copied entry-id
            $assocOrder[$entry->id] = $entryCopy->id;

            foreach ($entry->enablingObjectiveSubscriptions as $enabling) {
                EnablingObjectiveSubscriptions::Create([
                    'enabling_objective_id' => $enabling->enabling_objective_id,
                    'subscribable_type' => 'App\PlanEntry',
                    'subscribable_id' => $entryCopy->id,
                    'sharing_level_id' => $enabling->sharing_level_id,
                    'visibility' => $enabling->visibility,
                    'owner_id' => $ownerId,
                ]);
            }

            foreach ($entry->terminalObjectiveSubscriptions as $terminal) {
                TerminalObjectiveSubscriptions::Create([
                    'terminal_objective_id' => $terminal->terminal_objective_id,
                    'subscribable_type' => 'App\PlanEntry',
                    'subscribable_id' => $entryCopy->id,
                    'sharing_level_id' => $terminal->sharing_level_id,
                    'visibility' => $terminal->visibility,
                    'owner_id' => $ownerId,
                ]);
            }

            foreach ($entry->trainings as $training) {
                $newTraining = Training::Create([
                    'title' => $training->title,
                    'description' => $training->description,
                    'begin' => $training->begin,
                    'end' => $training->end,
                    'owner_id' => $ownerId,
                ]);

                TrainingSubscription::Create([
                    'training_id' => $newTraining->id,
                    'subscribable_type' => 'App\PlanEntry',
                    'subscribable_id' => $entryCopy->id,
                    'order_id' => 0,
                    'editable' => 1,
                    'owner_id' => $ownerId,
                ]);

                foreach ($training->exercises as $exercise) {
                    Exercise::Create([
                        'training_id' => $newTraining->id,
                        'title' => $exercise->title,
                        'description' => $exercise->description,
                        'recommended_iterations' => $exercise->recommended_iterations,
                        'owner_id' => $ownerId,
                    ]);
                }
            }
        }

        if ($plan->entry_order != null) {
            // set new entry_order based of associative array
            $newEntryOrder = array_map(function($entry_id) use (&$assocOrder) {
                return $assocOrder[$entry_id];
            }, $plan->entry_order);
        }

        $planCopy->entry_order = $newEntryOrder ?? null;
        $planCopy->save();

        return redirect('/plans');
    }
    /**
     * Get all users with the student-role that are enroled in a plan
     *
     * @param  \App\Plan $plan
     * @return Array Array of users
     */
    public function getUsers(Plan $plan)
    {
        $users = [];
        $subscriptions = $plan->subscriptions()->get()->toArray();
        // get every user-id through all subscriptions
        foreach ($subscriptions as $subscription) {
            // edit-rights are given to teachers or higher
            if ($subscription['editable']) continue;

            switch ($subscription['subscribable_type']) {
                case 'App\User':
                    array_push($users, $subscription['subscribable_id']);
                    break;
                case 'App\Group':
                    $ids = Group::find($subscription['subscribable_id'])->users()->get()->pluck('id')->toArray();
                    $users = array_merge($users, $ids);
                    break;
                case 'App\Organization':
                    $ids = Organization::find($subscription['subscribable_id'])->users()->get()->pluck('id')->toArray();
                    $users = array_merge($users, $ids);
                    break;
                default:
                    break;
            }
        }

        $users = array_unique($users, SORT_NUMERIC); // remove duplicates

        // query for the student role-id => 6
        $users = User::select('users.id', 'users.firstname', 'users.lastname')->whereIn('users.id', $users)
            ->join('organization_role_users', 'users.id', '=', 'organization_role_users.user_id')->where('organization_role_users.role_id', 6)
            ->distinct()->get()->toArray();

        return $users;
    }

    protected function validateRequest()
    {
        return request()->validate([
            'title'         => 'sometimes|required',
            'description'   => 'sometimes',
            'begin'         => 'sometimes',
            'end'           => 'sometimes',
            'duration'      => 'sometimes',
            'type_id'       => 'sometimes',
            'entry_order'   => 'sometimes',
            'color'         => 'sometimes',
            'allow_copy'    => 'sometimes',
        ]);
    }
}

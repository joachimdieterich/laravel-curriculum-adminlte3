<?php

namespace App\Http\Controllers;

use App\Organization;
use App\Plan;
use App\User;
use App\Group;
use App\PlanType;
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
        //TODO: only owner/admin should be able to delete a plan
        // theoretically a user enroled in a plan can send a delete request
        abort_unless((\Gate::allows('plan_delete') and $plan->isAccessible()), 403);

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
        ]);
    }
}

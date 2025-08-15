<?php

namespace App\Http\Controllers;

use App\Organization;
use App\Plan;
use App\PlanType;
use App\PlanEntry;
use App\User;
use App\Group;
use App\EnablingObjectiveSubscriptions;
use App\TerminalObjectiveSubscriptions;
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

        return view('plans.index');
    }

    protected function userPlans($withOwned = true)
    {
        $userCanSee = auth()->user()->plans;

        foreach (auth()->user()->currentGroups as $group) {
            $userCanSee = $userCanSee->merge($group->plans);
        }

        $organization = Organization::find(auth()->user()->current_organization_id)->plans;
        $userCanSee = $userCanSee->merge($organization);

        if ($withOwned) {
            $owned = Plan::where('owner_id', auth()->user()->id)->get();
            $userCanSee = $userCanSee->merge($owned);
        }

        return $userCanSee->unique();
    }

    public function list(Request $request)
    {
        abort_unless(\Gate::allows('plan_access'), 403);
        // $plans = (auth()->user()->role()->id == 1) ? Plan::all() : $this->userPlans();
        $plans = Plan::with('subscriptions');


        switch ($request->filter) {
            case 'owner':
                $plans = $plans->where('owner_id', auth()->user()->id);
                break;
            case 'shared_with_me':
                $plans = $this->userPlans(false);
                break;
            case 'shared_by_me':
                $plans = $plans->where('owner_id', auth()->user()->id)->whereHas('subscriptions');
                break;
            case 'all':
            default:
                $plans = $this->userPlans();
                break;
        }

        // $edit_gate = \Gate::allows('plan_edit');
        // $delete_gate = \Gate::allows('plan_delete');

        return DataTables::of($plans)
            // ->addColumn('action', function ($plans) use ($edit_gate, $delete_gate) {
            //     // actions should only be visible to owner. admin has all rights
            //     if ($plans->owner_id != auth()->user()->id && !is_admin()) return '';

            //     $actions = '';
            //     if ($edit_gate) {
            //         $actions .= '<a href="'.route('plans.edit', $plans->id).'"'
            //                         .'id="edit-plan-'.$plans->id.'" '
            //                         .'class="btn p-1">'
            //                         .'<i class="fa fa-pencil-alt"></i>'
            //                         .'</a>';
            //     }
            //     if ($delete_gate) {
            //         $actions .= '<button type="button" '
            //                     .'class="btn text-danger" '
            //                     .'onclick="destroyDataTableEntry(\'plans\','.$plans->id.')">'
            //                     .'<i class="fa fa-trash"></i></button>';
            //     }

            //     return $actions;
            // })

            // ->addColumn('check', '')
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
        abort(405);
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
            'color'             => $input['color'],
            'begin'             => $input['begin'],
            'end'               => $input['end'],
            'duration'          => $input['duration'],
            'type_id'           => format_select_input($input['type_id']),
            'allow_copy'          => $input['allow_copy'],
            'owner_id'          => auth()->user()->id,
        ]);

        // subscribe embedded media
        checkForEmbeddedMedia($plan, 'description');

        // axios call?
        if (request()->wantsJson()) {
            return ['message' => $plan->path()];
        }

        return redirect('/plans');
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
        $users = $editable ? $this->getUsers($plan) : [];

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
        abort(405);
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

        // subscribe embedded media
        checkForEmbeddedMedia($plan, 'description');

        return ['plan' => $plan];
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

    public function getTypes()
    {
        $types = PlanType::whereIn('id',
            explode(
                ',',
                \App\Config::where('key', 'availablePlanTypes')->get()->first()->value
            )
        );

        return getEntriesForSelect2ByCollection($types); //only show planstypes defined in config
        //return getEntriesForSelect2ByModel("App\PlanType");
    }

    public function copyPlan(Plan $plan)
    {
        $owner_id = auth()->user()->id;
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
            'owner_id' => $owner_id,
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
                'owner_id' => $owner_id,
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
                    'owner_id' => $owner_id,
                ]);
            }

            foreach ($entry->terminalObjectiveSubscriptions as $terminal) {
                TerminalObjectiveSubscriptions::Create([
                    'terminal_objective_id' => $terminal->terminal_objective_id,
                    'subscribable_type' => 'App\PlanEntry',
                    'subscribable_id' => $entryCopy->id,
                    'sharing_level_id' => $terminal->sharing_level_id,
                    'visibility' => $terminal->visibility,
                    'owner_id' => $owner_id,
                ]);
            }

            foreach ($entry->trainings as $training) {
                $newTraining = Training::Create([
                    'title' => $training->title,
                    'description' => $training->description,
                    'begin' => $training->begin,
                    'end' => $training->end,
                    'owner_id' => $owner_id,
                ]);

                TrainingSubscription::Create([
                    'training_id' => $newTraining->id,
                    'subscribable_type' => 'App\PlanEntry',
                    'subscribable_id' => $entryCopy->id,
                    'order_id' => 0,
                    'editable' => 1,
                    'owner_id' => $owner_id,
                ]);

                foreach ($training->exercises as $exercise) {
                    Exercise::Create([
                        'training_id' => $newTraining->id,
                        'title' => $exercise->title,
                        'description' => $exercise->description,
                        'recommended_iterations' => $exercise->recommended_iterations,
                        'owner_id' => $owner_id,
                    ]);
                }
            }
        }

        // set new entry_order based of associative array
        $newEntryOrder = array_map(function($entry_id) use (&$assocOrder) {
            return $assocOrder[$entry_id];
        }, $plan->entry_order);

        $planCopy->entry_order = $newEntryOrder;
        $planCopy->save();

        return redirect('/plans');
    }

    public function getUsers(Plan $plan)
    {
        $users = [];
        $subscriptions = $plan->subscriptions()->get()->toArray();
        // get every user-id through all subscriptions
        foreach ($subscriptions as $subscription) {
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

        // duplicates have to be removed, because SQL will return the same entry multiple times
        $users = array_unique($users, SORT_NUMERIC);

        // only get users that are students
        $users = User::select('users.id', 'users.firstname', 'users.lastname')->whereIn('users.id', $users)
            ->join('organization_role_users', 'users.id', '=', 'organization_role_users.user_id')->where('organization_role_users.role_id', 6)
            ->distinct()->get()->toArray();

        return $users;
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

    public function getUserAchievements(Plan $plan, $userIds)
    {
        $ids = explode(',', $userIds);
        $accessibleUserIds = array_map(function($user) { return $user['id']; }, $this->getUsers($plan));

        foreach ($ids as $id) {
            if (array_search($id, $accessibleUserIds) === false) {
                abort(403);
            }
        }

        $terminal = TerminalObjectiveSubscriptions::where('subscribable_type', 'App\\PlanEntry')
            ->whereIn('subscribable_id', $plan->entry_order)
            ->with([
                'terminalObjective.enablingObjectives',
                'terminalObjective.enablingObjectives.achievements' => function ($query) use ($ids) {
                    $query->whereIn('user_id', $ids);
                },
            ])
            ->get();

        $enabling = EnablingObjectiveSubscriptions::where('subscribable_type', 'App\\PlanEntry')
            ->whereIn('subscribable_id', $plan->entry_order)
            ->with([
                'enablingObjective',
                'enablingObjective.achievements'=> function ($query) use ($ids) {
                    $query->whereIn('user_id', $ids);
                },
            ])
            ->get();

        $users = User::find($ids);

        return view('plans.userAchievements')
            ->with(compact('terminal'))
            ->with(compact('enabling'))
            ->with(compact('users'));
    }

    protected function validateRequest()
    {
        return request()->validate([
            'title'         => 'sometimes|required',
            'description'   => 'sometimes',
            'color'         => 'sometimes',
            'begin'         => 'sometimes',
            'end'           => 'sometimes',
            'duration'      => 'sometimes',
            'type_id'       => 'sometimes',
            'allow_copy'    => 'sometimes',
            'entry_order'   => 'sometimes',
        ]);
    }
}

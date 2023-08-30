<?php

namespace App\Http\Controllers;

use App\Organization;
use App\Plan;
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

        return view('plans.index');
    }

    protected function userPlans()
    {
        $userCanSee = auth()->user()->plans;

        foreach (auth()->user()->currentGroups as $group) {
            $userCanSee = $userCanSee->merge($group->plans);
        }

        $organization = Organization::find(auth()->user()->current_organization_id)->plans;
        $userCanSee = $userCanSee->merge($organization);

        return $userCanSee->unique();
    }

    public function list()
    {
        abort_unless(\Gate::allows('plan_access'), 403);
        $plans = (auth()->user()->role()->id == 1) ? Plan::all() : $this->userPlans();

        $edit_gate = \Gate::allows('plan_edit');
        $delete_gate = \Gate::allows('plan_delete');

        return DataTables::of($plans)
            ->addColumn('action', function ($plans) use ($edit_gate, $delete_gate) {
                $actions = '';
                if ($edit_gate) {
                    $actions .= '<a href="'.route('plans.edit', $plans->id).'"'
                                    .'id="edit-plan-'.$plans->id.'" '
                                    .'class="btn p-1">'
                                    .'<i class="fa fa-pencil-alt"></i>'
                                    .'</a>';
                }
                if ($delete_gate) {
                    $actions .= '<button type="button" '
                                .'class="btn text-danger" '
                                .'onclick="destroyDataTableEntry(\'plans\','.$plans->id.')">'
                                .'<i class="fa fa-trash"></i></button>';
                }

                return $actions;
            })

            ->addColumn('check', '')
            ->setRowId('id')
            ->setRowAttr([
                'color' => 'primary',
            ])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_unless(\Gate::allows('plan_create'), 403);

        $plan = new Plan();
        //$types = PlanType::all()
        $types = PlanType::whereIn('id',
                explode(
                    ',',
                    \App\Config::where('key', 'availablePlanTypes')->get()->first()->value
                )
            )->get();

        return view('plans.create')
                ->with(compact('types'))
                ->with(compact('plan'));
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

        if (request()->wantsJson()) {
            return [
                'plan' => $plan,
            ];
        }

        return view('plans.show')
            ->with(compact('plan'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Plan  $plan
     * @return \Illuminate\Http\Response
     */
    public function edit(Plan $plan)
    {
        abort_unless((\Gate::allows('plan_edit') and $plan->isAccessible()), 403);
        $types = PlanType::whereIn('id',
            explode(
                ',',
                \App\Config::where('key', 'availablePlanTypes')->get()->first()->value
            )
        )->get();

        return view('plans.edit')
                ->with(compact('plan'))
                ->with(compact('types'));
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
            $clean_data['type_id'] = format_select_input($clean_data['type_id']);  //hack to prevent array to string conversion
        }

        $plan->update($clean_data);

        // subscribe embedded media
        checkForEmbeddedMedia($plan, 'description');

        return redirect($plan->path());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Plan  $plan
     * @return \Illuminate\Http\Response
     */
    public function destroy(Plan $plan)
    {
        abort_unless((\Gate::allows('plan_delete') and $plan->isAccessible()), 403);

        $plan->subscriptions()->delete();
        $plan->delete();

        return back();
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
        ]);
    }
}

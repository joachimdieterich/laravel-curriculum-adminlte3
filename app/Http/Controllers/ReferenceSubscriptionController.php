<?php

namespace App\Http\Controllers;

use App\Curriculum;
use App\Reference;
use App\ReferenceSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ReferenceSubscriptionController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        abort_unless(\Gate::allows('objective_create'), 403);
        $new_subscription = $this->validateRequest();

        if (($new_subscription['subscribable_type'] == "App\TerminalObjective" and $new_subscription['terminal_objective_id'] == $new_subscription['subscribable_id'])
            or
            ($new_subscription['subscribable_type'] == "App\EnablingObjective" and $new_subscription['enabling_objective_id'] == $new_subscription['subscribable_id'])
           ) {
            return false;
        }

        $reference = Reference::Create([
            'id' => (string) Str::uuid(),
            'description' => $new_subscription['description'],
            'grade_id' => isset($new_subscription['grade_id']) ? $new_subscription['grade_id'] : Curriculum::find($new_subscription['curriculum_id'])->grade_id,
            'owner_id' => auth()->user()->id,
        ]);
        $subscription = ReferenceSubscription::Create([
            'reference_id' => $reference->id,
            'referenceable_type' => $new_subscription['subscribable_type'],
            'referenceable_id' => $new_subscription['subscribable_id'],
            'sharing_level_id' => isset($new_subscription['sharing_level_id']) ? $new_subscription['sharing_level_id'] : 1,
            'visibility' => isset($new_subscription['visibility']) ? $new_subscription['visibility'] : true,
            'owner_id' => auth()->user()->id,
        ]);

        if ($subscription) { //generate sibling
            $sibling = ReferenceSubscription::Create([
                'reference_id' => $reference->id,
                'referenceable_type' => ($new_subscription['enabling_objective_id'] != null) ? "App\EnablingObjective" : "App\TerminalObjective",
                'referenceable_id' => ($new_subscription['enabling_objective_id'] != null) ? $new_subscription['enabling_objective_id'] : $new_subscription['terminal_objective_id'],
                'sharing_level_id' => isset($new_subscription['sharing_level_id']) ? $new_subscription['sharing_level_id'] : 1,
                'visibility' => isset($new_subscription['visibility']) ? $new_subscription['visibility'] : true,
                'owner_id' => auth()->user()->id,
            ]);
        }
        //adding to $subscription->referenceable models referencing_curriculum_id
        $model = $subscription->referenceable;
        $curricula_ids = (array) $model->referencing_curriculum_id;
        if (! in_array($new_subscription['curriculum_id'], $curricula_ids)) {
            array_push($curricula_ids, $new_subscription['curriculum_id']);
        }

        $model->referencing_curriculum_id = $curricula_ids;
        $model->save();

        //adding to $subscription->referenceable models referencing_curriculum_id
        $curriculum_id = app()->make($sibling->referenceable_type)::where('id', $sibling->referenceable_id)->get()->first()->curriculum_id;

        $model2 = $sibling->referenceable;
        $curricula_ids = (array) $model2->referencing_curriculum_id;
        if (! in_array($curriculum_id, $curricula_ids)) {
            array_push($curricula_ids, $curriculum_id);
        }

        $model2->referencing_curriculum_id = $curricula_ids;
        $model2->save();

        if (request()->wantsJson()) {
            return ['message' => 'ok'];
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ReferenceSubscription  $referenceSubscription
     * @return \Illuminate\Http\Response
     */
    public function show(ReferenceSubscription $referenceSubscription)
    {
        abort_unless(auth()->user()->role()->id == 1, 403); //only admins for debug purposes
        $reference_subscription = ReferenceSubscription::where('reference_id', $referenceSubscription->reference_id)
                        ->with(['siblings' => function ($query) use ($referenceSubscription) {
                            $query->where('reference_id', $referenceSubscription->reference_id)
                                ->where('referenceable_id', '!=', $referenceSubscription->referenceable_id)
                                ->where('referenceable_type', '=', $referenceSubscription->referenceable_type);
                        }])->get();

        dd($reference_subscription);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ReferenceSubscription  $referenceSubscription
     * @return \Illuminate\Http\Response
     */
    public function siblings(ReferenceSubscription $referenceSubscription)
    {
        return ReferenceSubscription::where('reference_id', $referenceSubscription->reference_id)
                        ->with(['siblings' => function ($query) use ($referenceSubscription) {
                            $query->where('reference_id', $referenceSubscription->reference_id)
                                ->where('referenceable_id', '!=', $referenceSubscription->referenceable_id)
                                ->where('referenceable_type', '=', $referenceSubscription->referenceable_type);
                        }])->get();
    }

    protected function validateRequest()
    {
        return request()->validate([
            'curriculum_id' => 'sometimes|required',
            'grade_id' => 'sometimes',
            'terminal_objective_id' => 'sometimes|required',
            'enabling_objective_id' => 'sometimes',
            'subscribable_type' => 'required',
            'subscribable_id' => 'required',
            'description' => 'sometimes',
        ]);
    }
}

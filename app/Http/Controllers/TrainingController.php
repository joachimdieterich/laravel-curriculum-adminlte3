<?php

namespace App\Http\Controllers;

use App\Training;
use App\TrainingSubscription;
use Illuminate\Http\Request;

class TrainingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

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

        $training = Training::create([
            'title' => $input['title'],
            'description' => $input['description'],
            'begin' => $input['begin'],
            'end' => $input['end'],
            'owner_id' => auth()->user()->id,
        ]);

        $order_id = TrainingSubscription::where([
            'subscribable_type' => $input['subscribable_type'],
            'subscribable_id' => $input['subscribable_id'],
        ])->count();

        TrainingSubscription::create([
            'training_id' => $training->id,
            'subscribable_type' => $input['subscribable_type'],
            'subscribable_id' => $input['subscribable_id'],
            'order_id' => $order_id,
            'editable' => 1, //needed?
            'owner_id' => auth()->user()->id,
        ]);

        if (request()->wantsJson()) {
            return $training;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Training  $training
     * @return \Illuminate\Http\Response
     */
    public function show(Training $training)
    {
        abort_unless(\Gate::allows('plan_show'), 403);

        return view('trainings.show', compact('training'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Training  $training
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Training $training)
    {
        abort_unless((\Gate::allows('plan_edit') and $training->isAccessible()), 403);
        $input = $this->validateRequest();

        $training->update([
            'title'       => $input['title'] ?? $training->title,
            'description' => $input['description'] ?? $training->description,
            'begin'       => $input['begin'],
            'end'         => $input['end'],
        ]);

        // subscribe embedded media
        checkForEmbeddedMedia($training, 'description');

        return $training->without('subscriptions.subscribable')->find($training->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Training  $training
     * @return \Illuminate\Http\Response
     */
    public function destroy(Training $training)
    {
        abort_unless(\Gate::allows('plan_delete'), 403);

        // decrement order_id of all subscriptions with higher order_id
        $subscription = $training->subscriptions->first();
        TrainingSubscription::where([
            'subscribable_type' => $subscription->subscribable_type,
            'subscribable_id' => $subscription->subscribable_id,
            ['order_id', '>', $subscription->order_id],
        ])->decrement('order_id', 1);

        $training->delete(); //subscriptions will be deleted too, see booted function in Training.php

        if (request()->wantsJson()) {
            return true;
        }
    }

    protected function validateRequest()
    {
        return request()->validate([
            'id' => 'sometimes|integer|nullable',
            'title' => 'required|string',
            'description' => 'sometimes|string|nullable',
            'begin' => 'sometimes',
            'end' => 'sometimes',
            'subscribable_type' => 'sometimes|string|nullable',
            'subscribable_id' => 'sometimes|integer|nullable',
        ]);
    }
}

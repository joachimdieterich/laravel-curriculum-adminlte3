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
        $new_training = $this->validateRequest();

        $entry = Training::create([
            'title' => $new_training['title'],
            'description' => $new_training['description'],

            'begin' => $new_training['begin'],
            'end' => $new_training['end'],

            'owner_id' => auth()->user()->id,
        ]);

        $subscription = TrainingSubscription::create([
            'training_id' => $entry->id,
            'subscribable_type' => $new_training['subscribable_type'],
            'subscribable_id' => $new_training['subscribable_id'],
            'order_id' => $new_training['order_id'] ?? 0,
            'editable' => 1, //needed?
            'owner_id' => auth()->user()->id,
        ]);

        // subscribe embedded media
        checkForEmbeddedMedia($entry, 'description');


        if (request()->wantsJson()) {
            return ['entry' => $entry];
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
        $clean_data = $this->validateRequest();

        $training->update([
            'title'       => $clean_data['title'] ?? $training->title,
            'description' => $clean_data['description'] ?? $training->description,
            'begin'       => $clean_data['begin'] ?? $training->begin,
            'end'         => $clean_data['end'] ?? $training->end,
        ]);

        // subscribe embedded media
        checkForEmbeddedMedia($training, 'description');

        return $training;
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

        $training->delete(); //subscriptions will be deleted too see booted function in Training.php

        if (request()->wantsJson()) {
            return [
                'deleted' => true,
            ];
        }
    }

    protected function validateRequest()
    {
        return request()->validate([
            'id' => 'sometimes|integer|nullable',
            'title' => 'sometimes|string|nullable',
            'description' => 'sometimes|string|nullable',
            'begin' => 'sometimes',
            'end' => 'sometimes',
            'subscribable_type' => 'sometimes|string',
            'subscribable_id' => 'sometimes|integer',
            'owner_id' => 'sometimes|integer'
        ]);
    }
}

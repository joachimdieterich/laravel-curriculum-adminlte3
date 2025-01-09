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

        TrainingSubscription::create([
            'training_id' => $training->id,
            'subscribable_type' => $input['subscribable_type'],
            'subscribable_id' => $input['subscribable_id'],
            'order_id' => $input['order_id'] ?? 0,
            'editable' => 1, //needed?
            'owner_id' => auth()->user()->id,
        ]);

        // subscribe embedded media
        checkForEmbeddedMedia($training, 'description');

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

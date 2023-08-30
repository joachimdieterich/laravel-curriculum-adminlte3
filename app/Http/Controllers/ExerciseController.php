<?php

namespace App\Http\Controllers;

use App\Exercise;
use App\Training;
use Illuminate\Http\Request;

class ExerciseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $input = $this->validateRequest();
        if (isset($input['training_id']) ) {
            $model = Training::find($input['training_id']);
            abort_unless((\Gate::allows('plan_access') and $model->isAccessible()), 403);

            $exercises = $model->exercises()
                ->with(['dones' => function ($query) {
                    $query->where('owner_id', auth()->user()->id);
                }])->get();


            if (request()->wantsJson()) {
                return [
                    'exercises' => $exercises];
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
        abort_unless(\Gate::allows('plan_create'), 403);
        $new_exercise = $this->validateRequest();

        $entry = Exercise::create([
            'title' => $new_exercise['title'],
            'description' => $new_exercise['description'],
            'recommended_iterations' => $new_exercise['recommended_iterations'] ?? 1,
            'training_id' => $new_exercise['training_id'],
            'order_id' => $new_training['order_id'] ?? 0,
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
     * @param  \App\Exercise  $exercise
     * @return \Illuminate\Http\Response
     */
    public function show(Exercise $exercise)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Exercise  $exercise
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Exercise $exercise)
    {
        abort_unless(\Gate::allows('plan_delete'), 403);
        $update_exercise = $this->validateRequest();
        $exercise->update([
            'title' => $update_exercise['title'],
            'description' => $update_exercise['description'],
            'recommended_iterations' => $update_exercise['recommended_iterations'] ?? 1,
            'training_id' => $update_exercise['training_id'],
            'order_id' => $update_exercise['order_id'] ?? 0,
        ]);

        // subscribe embedded media
        checkForEmbeddedMedia($exercise, 'description');

        // axios call?
        if (request()->wantsJson()) {
            return ['entry' => $exercise];
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Exercise  $exercise
     * @return \Illuminate\Http\Response
     */
    public function destroy(Exercise $exercise)
    {
        abort_unless(\Gate::allows('plan_delete'), 403);

        $exercise->delete(); // ExerciseDones are deleted via boot method

        if (request()->wantsJson()) {
            return [
                'deleted' => true,
            ];
        }//
    }

    protected function validateRequest()
    {
        return request()->validate([
            'training_id' => 'sometimes|integer',
            'title'   => 'sometimes|string',
            'description'   => 'sometimes|string|nullable',
            'recommended_iterations'   => 'sometimes|integer',
            'order_id'   => 'sometimes|integer',
        ]);
    }
}

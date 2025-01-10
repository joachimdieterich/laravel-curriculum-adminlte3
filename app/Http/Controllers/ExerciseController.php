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
        $model = Training::find(request()->training_id);
        abort_unless((\Gate::allows('plan_access') and $model->isAccessible()), 403);

        $exercises = $model->exercises()
            ->select('id', 'title', 'description', 'recommended_iterations')
            ->with(['dones' => function ($query) {
                $query->select('id', 'exercise_id', 'iterations', 'created_at')
                    ->where('owner_id', auth()->user()->id);
            }])->get();

        if (request()->wantsJson()) {
            return $exercises;
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
        $input = $this->validateRequest();

        $exercise = Exercise::create([
            'title' => $input['title'],
            'description' => $input['description'],
            'recommended_iterations' => $input['recommended_iterations'] ?? 1,
            'training_id' => $input['training_id'],
            'order_id' => $input['order_id'] ?? 0,
            'owner_id' => auth()->user()->id,
        ]);
        // subscribe embedded media
        checkForEmbeddedMedia($exercise, 'description');

        if (request()->wantsJson()) {
            return $exercise;
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
        abort_unless(\Gate::allows('plan_edit'), 403);
        $input = $this->validateRequest();

        $exercise->update([
            'title' => $input['title'],
            'description' => $input['description'],
            'recommended_iterations' => $input['recommended_iterations'] ?? 1,
            'training_id' => $input['training_id'],
            'order_id' => $input['order_id'] ?? 0,
        ]);

        // subscribe embedded media
        checkForEmbeddedMedia($exercise, 'description');

        if (request()->wantsJson()) {
            return $exercise;
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
            return true;
        }
    }

    protected function validateRequest()
    {
        return request()->validate([
            'training_id' => 'required|integer',
            'title' => 'required|string',
            'description' => 'sometimes|string|nullable',
            'recommended_iterations' => 'required|integer',
            'order_id' => 'sometimes|integer',
        ]);
    }
}

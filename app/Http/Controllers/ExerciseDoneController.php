<?php

namespace App\Http\Controllers;

use App\ExerciseDone;
use App\Exercise;
use Illuminate\Http\Request;

class ExerciseDoneController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $input = $this->validateRequest();
        if (isset($input['exercise_id']) ) {
            $model = Exercise::find($input['exercise_id']);
            abort_unless((\Gate::allows('plan_access') and $model->isAccessible()), 403);

            if (request()->wantsJson()) {
                return ['exercise' => $model->load('dones')];
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
        abort_unless(\Gate::allows('achievement_create_self_assessment'), 403);
        $new_iteration = $this->validateRequest();

        $entry = ExerciseDone::create([
            'exercise_id' => $new_iteration['exercise_id'],
            'iterations' => $new_iteration['iterations'],

            'user_id' => $new_iteration['user_id'] ?? auth()->user()->id,
            'owner_id' => auth()->user()->id,
        ]);

        if (request()->wantsJson()) {
            return ['entry' => $entry];
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ExerciseDone  $exerciseDone
     * @return \Illuminate\Http\Response
     */
    public function show(ExerciseDone $exerciseDone)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ExerciseDone  $exerciseDone
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ExerciseDone $exerciseDone)
    {
        abort_unless(\Gate::allows('achievement_create_self_assessment'), 403);
        $update_iteration = $this->validateRequest();

        $exerciseDone->update([
            'iterations' => $update_iteration['iterations'],
        ]);

        // axios call?
        if (request()->wantsJson()) {
            return ['entry' => $exerciseDone];
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ExerciseDone  $exerciseDone
     * @return \Illuminate\Http\Response
     */
    public function destroy(ExerciseDone $exerciseDone)
    {
        abort_unless(\Gate::allows('achievement_create_self_assessment'), 403);

        $exerciseDone->delete();

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
            'exercise_id' => 'sometimes|integer',
            'iterations' => 'sometimes|integer',
            'user_id' => 'sometimes|integer',
            'owner_id' => 'sometimes|integer'
        ]);
    }
}

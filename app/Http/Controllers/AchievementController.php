<?php

namespace App\Http\Controllers;

use App\Achievement;
use App\EnablingObjective;
use Illuminate\Http\Request;
use App\Http\Controllers\ProgressController;

class AchievementController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        abort_unless(\Gate::allows('achievement_create'), 403);

        $input = $this->validateRequest();

        $user_ids = !empty($input['user_id']) ? $input['user_id']: auth()->user()->id;

        foreach((array) $user_ids AS $user_id)
        {
            $achievement = Achievement::where('referenceable_type', '=', $input['referenceable_type'])
                                      ->where('referenceable_id', '=', $input['referenceable_id'])
                                      ->where('user_id', '=', $user_id)->first();

                $achievement = Achievement::updateOrCreate(
                    [
                        "referenceable_type" => $input['referenceable_type'],
                        "referenceable_id"   => $input['referenceable_id'],
                        "user_id"            => $user_id,
                    ],
                    [
                        "status"             => $this->calculateStatus($user_id, $input, ($achievement === null) ? '00' : $achievement->status),
                        "owner_id"           => auth()->user()->id,
                    ]
                );

            $achievement->save();

            $obj = EnablingObjective::find($input['referenceable_id']);
            (new ProgressController)->calculateProgress('App\TerminalObjective', $obj->terminal_objective_id, $user_id);
        }
        // axios call?
        if (request()->wantsJson()){
            return ['message' => $achievement->status];
        }
        return $achievement;
    }

    /* calculate proper status id */
    protected function calculateStatus($user_id, $input, $status = '00')
    {
        if(\Gate::allows('achievement_create') AND $user_id != auth()->user()->id)
        {
            $status[1] = $input['status'];
        }
        else // self assesment
        {
            $status[0] = $input['status'];
        }
        return $status;
    }

    protected function validateRequest()
    {
        return request()->validate([
            'referenceable_type'    => 'required',
            'referenceable_id'      => 'required',
            'user_id'               => 'sometimes',
            'status'                => 'required'
            ]);
    }
}

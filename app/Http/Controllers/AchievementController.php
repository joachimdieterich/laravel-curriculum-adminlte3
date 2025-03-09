<?php

namespace App\Http\Controllers;

use App\Achievement;
use App\EnablingObjective;
use App\User;
use Illuminate\Http\Request;

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
        abort_unless((\Gate::allows('achievement_create') or \Gate::allows('achievement_create_self_assessment')), 403);

        $input = $this->validateRequest();

        $user_ids = ! empty($input['user_id']) ? $input['user_id'] : auth()->user()->id;

        foreach ((array) $user_ids as $user_id) {
            abort_unless(auth()->user()->mayAccessUser(User::find($user_id)), 403);

            $achievement = Achievement::where('referenceable_type', '=', $input['referenceable_type'])
                ->where('referenceable_id', '=', $input['referenceable_id'])
                ->where('user_id', '=', $user_id)
                ->first();

            $achievement = Achievement::updateOrCreate(
                [
                    'referenceable_type' => $input['referenceable_type'],
                    'referenceable_id'   => $input['referenceable_id'],
                    'user_id'            => $user_id,
                ],
                [
                    'status'             => $this->calculateStatus($user_id, $input, ($achievement === null) ? '00' : $achievement->status),
                    'owner_id'           => auth()->user()->id,
                ]
            );

            $achievement->save();

            $obj = EnablingObjective::find($input['referenceable_id']);
            (new ProgressController)->calculateProgress('App\TerminalObjective', $obj->terminal_objective_id, $user_id);
        }

        LogController::set(get_class($this).'@'.__FUNCTION__, auth()->user()->role()->id, count((array) $user_ids));

        if (request()->wantsJson()) {
            return Achievement::whereIn('user_id',  (array) $user_ids)
                ->where('referenceable_id', '=', $input['referenceable_id'])
                ->where('referenceable_type', '=', $input['referenceable_type'])
                ->with([
                    'owner' => function($query) {
                        $query->select('id', 'firstname', 'lastname');
                    },
                    'user' => function($query) {
                        $query->select('id', 'firstname', 'lastname');
                    },
                ])
                ->get();
        }

        return $achievement;
    }

    /* calculate proper status id */
    protected function calculateStatus($user_id, $input, $status = '00')
    {
        if (\Gate::allows('achievement_create') and $user_id != auth()->user()->id) {
            abort_unless((auth()->user()->role()->id <= 5), 403); //only Teacher and roles above
            $status[1] = $input['status'];
        } elseif (\Gate::allows('achievement_create_self_assessment')) { // self_assessment
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
            'status'                => 'required',
        ]);
    }
}

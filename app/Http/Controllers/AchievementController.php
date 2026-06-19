<?php

namespace App\Http\Controllers;

use App\Achievement;
use App\AchievementHistory;
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

        $user_ids = !empty($input['user_id']) ? (array) $input['user_id'] : [auth()->user()->id];

        foreach ($user_ids as $user_id) {
            abort_unless(auth()->user()->mayAccessUser(User::find($user_id)), 403, "User $user_id is not enroled in current organization.");

            $achievement = Achievement::where('referenceable_type', $input['referenceable_type'])
                ->where('referenceable_id', $input['referenceable_id'])
                ->where('user_id', $user_id)
                ->first();
            $new_status = $this->calculateStatus($user_id, $input, $achievement?->status ?? '00');

            if ($achievement) {
                $this->preserveStatus($achievement);

                $achievement->status = $new_status;
                $achievement->owner_id = auth()->user()->id;

                $achievement->save();
            } else {
                Achievement::create(
                    [
                        'referenceable_type' => $input['referenceable_type'],
                        'referenceable_id'   => $input['referenceable_id'],
                        'user_id'            => $user_id,
                        'status'             => $new_status,
                        'owner_id'           => auth()->user()->id,
                    ]
                );
            }

            if ($input['referenceable_type'] === 'App\\EnablingObjective') {
                $obj = EnablingObjective::select('terminal_objective_id')
                    ->without(['terminalObjective', 'level'])
                    ->find($input['referenceable_id']);
                (new ProgressController)->calculateProgress('App\TerminalObjective', $obj->terminal_objective_id, $user_id);
            }
        }

        LogController::set(get_class($this).'@'.__FUNCTION__, auth()->user()->role()->id, count($user_ids));

        return Achievement::whereIn('user_id', $user_ids)
            ->where('referenceable_id', $input['referenceable_id'])
            ->where('referenceable_type', $input['referenceable_type'])
            ->with([
                'owner:id,firstname,lastname',
                'user:id,firstname,lastname',
            ])
            ->get();
    }

    /* calculate proper status id */
    protected function calculateStatus(int $user_id, array $input, $status = '00'): string
    {
        if (\Gate::allows('achievement_create') and $user_id != auth()->user()->id) {
            abort_unless((auth()->user()->role()->id <= 5), 403); //only Teacher and roles above
            $status[1] = $input['status'];
        } else if (\Gate::allows('achievement_create_self_assessment')) {
            $status[0] = $input['status'];
        }

        return $status;
    }

    protected function preserveStatus(Achievement $achievement): void
    {
        AchievementHistory::create([
            'achievement_id'    => $achievement->id,
            'status'            => $achievement->status,
            'owner_id'          => $achievement->owner_id,
        ]);
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

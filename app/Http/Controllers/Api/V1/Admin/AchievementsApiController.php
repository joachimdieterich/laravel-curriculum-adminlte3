<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Achievement;
use App\AchievementScale;
use App\EnablingObjective;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ProgressController;
use App\User;


class AchievementsApiController extends Controller
{

    public function store()
    {
        $user_id = User::where('common_name', request()->input('user_common_name'))->get()->first();
        $owner_id = User::where('common_name', request()->input('owner_common_name'))->get()->first();

        $achievement = Achievement::updateOrCreate(
            [
                'referenceable_type' => request()->input('referenceable_type'),
                'referenceable_id'   => request()->input('referenceable_id'),
                'scale_id'           => AchievementScale::where('title', request()->input('scale'))->first()->id ?? 2,
                'user_id'            => $user_id->id,
            ],
            [
                'status'             => request()->input('status'),
                'owner_id'           => $owner_id->id
            ]
        );

        $achievement->save();

        $obj = EnablingObjective::find(request()->input('referenceable_id'));
        (new ProgressController)->calculateProgress('App\TerminalObjective', $obj->terminal_objective_id, $user_id->id);

        return Achievement::where('user_id', $user_id->id)
            ->where('referenceable_id', '=', request()->input('referenceable_id'))
            ->where('referenceable_type', '=', request()->input('referenceable_type'))
            ->with([
                'owner:id,firstname,lastname',
                'user:id,firstname,lastname',
            ])
            ->get();
}
    public function show(Achievement $achievement)
    {

    }
}

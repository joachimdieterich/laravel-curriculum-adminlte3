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
        $input = request()->all();

        if (
            !isset($input['referenceable_type'])
            or !isset($input['referenceable_id'])
            or !isset($input['scale'])
            or !isset($input['status'])
            or !isset($input['user_common_name'])
            or !isset($input['owner_common_name'])
        ) {
            return response()->json('Missing required fields', 400);
        }

        $user_id = User::where('common_name', $input['user_common_name'])->pluck('id')->first();
        $owner_id = User::where('common_name', $input['owner_common_name'])->pluck('id')->first();
        $scale_id = AchievementScale::where('title', strtolower($input['scale']))->pluck('id')->first();

        if (!$user_id) return response()->json('User not found: ' . $input['user_common_name'], 404);
        if (!$owner_id) return response()->json('Owner not found: ' . $input['user_common_name'], 404);
        if (!$scale_id) return response()->json('Scale not found: ' . $input['scale'], 404);

        $achievement = Achievement::updateOrCreate(
            [
                'referenceable_type' => $input['referenceable_type'],
                'referenceable_id'   => $input['referenceable_id'],
                'scale_id'           => $scale_id,
                'user_id'            => $user_id,
            ],
            [
                'status'             => $input['status'],
                'owner_id'           => $owner_id,
            ]
        );

        $achievement->save();

        $terminal_id = EnablingObjective::where('id', $input['referenceable_id'])->pluck('terminal_objective_id')->first();
        (new ProgressController)->calculateProgress('App\TerminalObjective', $terminal_id, $user_id);

        return Achievement::select('id', 'referenceable_type', 'referenceable_id', 'user_id', 'owner_id', 'status')
            ->where([
                'user_id' => $user_id,
                'scale_id' => $scale_id,
                'referenceable_id' => $input['referenceable_id'],
                'referenceable_type' => $input['referenceable_type'],
            ])
            ->with([
                'owner:id,firstname,lastname',
                'user:id,firstname,lastname',
            ])
            ->first();
}
    public function show(Achievement $achievement)
    {

    }
}
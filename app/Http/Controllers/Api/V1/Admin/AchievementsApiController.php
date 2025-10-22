<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Achievement;
use App\AchievementScale;
use App\EnablingObjective;
use App\Http\Controllers\Controller;
use App\User;


class AchievementsApiController extends Controller
{
    public function store()
    {
        $input = request()->all();

        if (
            !isset($input['referenceable_id'])
            or !isset($input['scale'])
            or !isset($input['status'])
            or !isset($input['user_common_name'])
            or !isset($input['owner_common_name'])
        ) {
            return response()->json('Missing required fields', 400);
        }
        // decode referenceable_id and user_common_name if sent as strings
        if (gettype($input['referenceable_id']) == 'string') $input['referenceable_id'] = json_decode($input['referenceable_id'], true);
        if (gettype($input['user_common_name']) == 'string') $input['user_common_name'] = json_decode($input['user_common_name'], true);
        // check if both are now arrays
        if (!is_array($input['referenceable_id']) || !is_array($input['user_common_name'])) {
            return response()->json('Invalid data format, arrays expected for referenceable_id and user_common_name', 400);
        }

        $user_ids = User::select('id', 'common_name')->whereIn('common_name', $input['user_common_name'])->get();
        $owner_id = User::where('common_name', $input['owner_common_name'])->pluck('id')->first();
        $scale_id = AchievementScale::where('title', strtolower($input['scale']))->pluck('id')->first();

        if (count($user_ids) != count($input['user_common_name'])) {
            $diff = array_diff($input['user_common_name'], $user_ids->pluck('common_name')->toArray());
            return response()->json('User not found: ' . implode($diff), 404);
        }
        if (!$owner_id) return response()->json('Owner not found: ' . $input['owner_common_name'], 404);
        if (!$scale_id) return response()->json('Scale not found: ' . $input['scale'], 404);

        $user_ids = $user_ids->pluck('id');

        foreach ($user_ids as $user_id) {
            foreach ($input['referenceable_id'] as $ref_id) {
                Achievement::updateOrCreate(
                    [
                        'referenceable_type' => 'App\\EnablingObjective',
                        'referenceable_id'   => $ref_id,
                        'scale_id'           => $scale_id,
                        'user_id'            => $user_id,
                    ],
                    [
                        'status'             => $input['status'],
                        'owner_id'           => $owner_id,
                    ]
                );
            }
        }

        return EnablingObjective::select('id')
            ->whereIn('id', $input['referenceable_id'])
            ->without(['terminalObjective', 'level'])
            ->with(['achievements'  => function ($query) use ($user_ids, $scale_id) {
                $query->select('achievements.id', 'referenceable_id', 'users.common_name AS user', 'status')
                    ->join('users', 'users.id', '=', 'achievements.user_id')
                    ->whereIn('user_id', $user_ids)
                    ->where('scale_id', $scale_id);
            }])
            ->get();
}
    public function getAchievements()
    {
        $input = request()->all();

        if (
            !isset($input['referenceable_id'])
            or !isset($input['scale'])
        ) {
            return response()->json('Missing required fields', 400);
        }

        $scale_id = AchievementScale::where('title', strtolower($input['scale']))->pluck('id')->first();

        if (!$scale_id) return response()->json('Scale not found: ' . $input['scale'], 404);
        
        return Achievement::select('id', 'referenceable_type', 'referenceable_id', 'user_id', 'owner_id', 'status')
            ->where([
                'referenceable_id' => $input['referenceable_id'],
                'referenceable_type' => 'App\\EnablingObjective',
                'scale_id' => $scale_id,
            ])
            ->with([
                'owner:id,firstname,lastname',
                'user:id,firstname,lastname',
            ])
            ->get();
    }
}
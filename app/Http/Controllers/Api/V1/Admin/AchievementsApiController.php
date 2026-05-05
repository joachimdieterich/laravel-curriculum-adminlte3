<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Achievement;
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
            or !isset($input['status'])
            or !isset($input['user_common_name'])
            or !isset($input['owner_common_name'])
        ) {
            return response()->json('Missing required fields', 400);
        }
        if (strlen($input['status']) !== 2 || !preg_match('/[0-9]/', $input['status'])) {
            return response()->json('Invalid status format, expected 2 characters with at least one number', 400);
        }
        // decode referenceable_id and user_common_name if sent as strings
        if (gettype($input['referenceable_id']) == 'string') $input['referenceable_id'] = json_decode($input['referenceable_id'], true);
        if (gettype($input['user_common_name']) == 'string') $input['user_common_name'] = json_decode($input['user_common_name'], true);
        // check if both are now arrays
        if (!is_array($input['referenceable_id']) || !is_array($input['user_common_name'])) {
            return response()->json('Invalid data format, expected array for referenceable_id and user_common_name', 400);
        }

        $user_ids = User::select('id', 'common_name')->whereIn('common_name', $input['user_common_name'])->get();
        $owner_id = User::where('common_name', $input['owner_common_name'])->pluck('id')->first();

        if (count($user_ids) != count($input['user_common_name'])) {
            $diff = array_diff($input['user_common_name'], $user_ids->pluck('common_name')->toArray());
            return response()->json('User with common_name not found: ' . implode($diff), 404);
        }
        if (!$owner_id) return response()->json('Owner not found: ' . $input['owner_common_name'], 404);

        $user_ids = $user_ids->pluck('id');

        foreach ($user_ids as $user_id) {
            foreach ($input['referenceable_id'] as $ref_id) {
                $status = $input['status'];
                $whitecard = strpos(strtolower($input['status']), 'x');
                // fill whitecard slot with previous value if exists, otherwise default to 0
                if ($whitecard !== false) {
                    $achievement = Achievement::select('status')->where([
                        'referenceable_type' => 'App\\EnablingObjective',
                        'referenceable_id'   => $ref_id,
                        'user_id'            => $user_id,
                    ])->first();
    
                    if ($achievement == null) {
                        $status[$whitecard] = 0;
                    } else {
                        $status[$whitecard] = $achievement->status[$whitecard];
                    }
                }

                Achievement::updateOrCreate(
                    [
                        'referenceable_type' => 'App\\EnablingObjective',
                        'referenceable_id'   => $ref_id,
                        'user_id'            => $user_id,
                    ],
                    [
                        'status'             => $status,
                        'owner_id'           => $owner_id,
                    ]
                );
            }
        }

        return EnablingObjective::select('id')
            ->whereIn('id', $input['referenceable_id'])
            ->without(['terminalObjective', 'level'])
            ->with(['achievements'  => function ($query) use ($user_ids) {
                $query->select('achievements.id', 'referenceable_id', 'users.common_name AS user', 'status')
                    ->join('users', 'users.id', '=', 'achievements.user_id')
                    ->whereIn('user_id', $user_ids);
            }])
            ->get();
    }

    public function getAchievements()
    {
        $input = request()->all();

        if (!isset($input['referenceable_id'])) {
            return response()->json('Missing required fields', 400);
        }
        // check referenceable_id format
        if (gettype($input['referenceable_id']) == 'string') $input['referenceable_id'] = json_decode($input['referenceable_id'], true);
        if (!is_array($input['referenceable_id'])) return response()->json('Invalid data format, expected array for referenceable_id', 400);

        $user_ids = null;
        // optional user_common_name filter
        if (isset($input['user_common_name']) && gettype($input['user_common_name']) == 'string') {
            $input['user_common_name'] = json_decode($input['user_common_name'], true);
            if (!is_array($input['user_common_name'])) return response()->json('Invalid data format, expected array for user_common_name', 400);

            $user_ids = User::select('id', 'common_name')->whereIn('common_name', $input['user_common_name'])->get();
            if (count($user_ids) != count($input['user_common_name'])) {
                $diff = array_diff($input['user_common_name'], $user_ids->pluck('common_name')->toArray());
                return response()->json('User with common_name not found: ' . implode($diff), 404);
            }
            $user_ids = $user_ids->pluck('id');
        }
        
        return EnablingObjective::select('id')
            ->whereIn('id', $input['referenceable_id'])
            ->without(['terminalObjective', 'level'])
            ->with(['achievements'  => function ($query) use ($user_ids) {
                $query->select('achievements.id', 'referenceable_id', 'users.common_name AS user', 'status')
                    ->join('users', 'users.id', '=', 'achievements.user_id');
                // filter by user_ids if given
                if ($user_ids) $query->whereIn('user_id', $user_ids);
            }])
            ->get();
    }
}
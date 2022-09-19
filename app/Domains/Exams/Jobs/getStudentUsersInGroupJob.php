<?php

namespace App\Domains\Exams\Jobs;

use App\User;

class getStudentUsersInGroupJob
{
    public static function get(int $group_id)
    {
        $users = User::select('id')->whereHas('groups', function ($query) use ($group_id) {
            $query->whereGroupId($group_id);
        })->with('groups:id')->whereHas('roles', function ($query) {
            $query->whereTitle('student');
        })->with('roles:title')->get()->toArray();

        return array_column($users, 'id');
    }
}

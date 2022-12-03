<?php

namespace App\Domains\Exams\Jobs\IleaPlusJobs;

use Illuminate\Support\Facades\DB;

class getUsersIleaIdsJob
{
    public static function run(int $exam_id, array $user_ids)
    {
        $login_data = DB::table('exam_user')
            ->whereIn('user_id', $user_ids)
            ->where('exam_started', false)
            ->select('user_id', 'login_data')
            ->where('exam_id', $exam_id)->get()->toArray();

        foreach ($login_data as $login) {
            $data['login_data'][] = json_decode($login->login_data)->ilea_id;
            $data['user_ids'][] = $login->user_id;
        }

        return $data;
    }
}

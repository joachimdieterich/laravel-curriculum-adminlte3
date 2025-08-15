<?php

namespace App\Domains\Exams\Jobs\IleaPlusJobs;

use App\Domains\Exams\Models\Exam;

class findStudentByIdJob
{
    public static function get(Exam $exam, int $student_id)
    {
        return $exam
            ->users()
            ->whereJsonContains('exam_user.login_data->ilea_id', $student_id)
            ->first();
    }
}

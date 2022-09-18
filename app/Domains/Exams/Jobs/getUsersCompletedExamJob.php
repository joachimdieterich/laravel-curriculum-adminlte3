<?php

namespace App\Domains\Exams\Jobs;

use App\Domains\Exams\Models\Exam;

class getUsersCompletedExamJob
{
    public static function get(Exam $exam)
    {
        return $exam->users()->wherePivot('exam_completed_at', '!=', null)->count();
    }
}

<?php

namespace App\Domains\Exams\Jobs;

use App\Domains\Exams\Models\Exam;

class getExamStatusJob
{
    public static function get(Exam $exam): int
    {
        $total_students = $exam->users()->count();
        $students_completed = getUsersCompletedExamJob::get($exam);

        return ($total_students === 0) ? 0 : ($students_completed / $total_students * 100);
    }
}

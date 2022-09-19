<?php

namespace App\Domains\Exams\Jobs;

use App\Domains\Exams\Models\Exam;

class setExamStatusJob
{
    public static function set(Exam $exam, int $status): void
    {
        $exam->update([
            'status' => $status
        ]);
    }
}

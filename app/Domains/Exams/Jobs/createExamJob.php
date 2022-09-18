<?php

namespace App\Domains\Exams\Jobs;

use App\Domains\Exams\Models\Exam;

class createExamJob
{
    public static function run(string $tool, string $exam_id, string $test_name, string $school_key, string $subject, string $tutorial_key, int $created_by, int $group_id): Exam
    {
        return Exam::firstOrCreate([
            'tool' => $tool,
            'exam_id' => $exam_id,
            'test_name' => $test_name,
            'school_key' => $school_key,
            'subject' => $subject,
            'tutorial_key' => $tutorial_key,
            'created_by' => $created_by,
            'group_id' => $group_id
        ])->refresh();
    }
}

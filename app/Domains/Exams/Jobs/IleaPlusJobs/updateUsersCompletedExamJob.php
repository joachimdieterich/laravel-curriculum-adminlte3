<?php

namespace App\Domains\Exams\Jobs\IleaPlusJobs;

use App\Domains\Exams\Models\Exam;

class updateUsersCompletedExamJob
{
    public static function update(array $students, Exam $exam): void
    {
        foreach ($students as $student) {
            $user = findStudentByIdJob::get($exam, $student->id);
            if (isset($user)) {
                $user->exams()
                    ->update([
                        'exam_completed_at' => isset($student->completed_at) ? $student->completed_at : null,
                        'exam_started' => true
                    ]);
            }
        }
    }
}

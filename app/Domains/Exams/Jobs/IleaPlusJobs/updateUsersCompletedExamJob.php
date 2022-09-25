<?php

namespace App\Domains\Exams\Jobs\IleaPlusJobs;

use App\Domains\Exams\Models\Exam;

class updateUsersCompletedExamJob
{
    public static function update(array $students, Exam $exam): void
    {
        $exam_id = $exam->getAttributeValue("id");
        foreach ($students as $student) {
            $user = findStudentByIdJob::get($exam, $student->id);
            if (isset($user)) {
                $user->exams()
                    ->where("exam_user.exam_id", $exam_id)
                    ->update([
                        'exam_completed_at' => isset($student->completed_at) ? $student->completed_at : null,
                        'exam_started' => true
                    ]);
            }
        }
    }
}

<?php

namespace App\Domains\Tests\Interfaces;

use App\Domains\Exams\Models\Exam;

interface TestToolkitInterface
{
    public function getTests();

    public function createExam(int $group_id, int $test_id, string $test_name);

    public function deleteExam(Exam $exam);

    public function getExamLoginUrl(Exam $exam);

    public function getExamStatus(Exam $exam);

    public function addUsers(Exam $exam, array $user_ids);

    public function removeUsers(Exam $exam, array $user_ids);

    public function getReport(Exam $exam);
}

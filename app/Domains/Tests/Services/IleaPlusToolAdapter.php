<?php

namespace App\Domains\Tests\Services;

use App\Domains\Exams\Jobs\createExamJob;
use App\Domains\Exams\Jobs\getExamStatusJob;
use App\Domains\Exams\Jobs\getStudentUsersInGroupJob;
use App\Domains\Exams\Jobs\IleaPlusJobs\decodeTanJob;
use App\Domains\Exams\Jobs\IleaPlusJobs\updateUsersCompletedExamJob;
use App\Domains\Exams\Jobs\IleaPlusJobs\getStudentsDataJob;
use App\Domains\Exams\Jobs\IleaPlusJobs\getUsersIleaIdsJob;
use App\Domains\Exams\Jobs\setExamStatusJob;
use App\Domains\Exams\Models\Exam;
use App\Domains\Tests\Interfaces\TestToolkitInterface;
use App\Group;
use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\Response;

class IleaPlusToolAdapter implements TestToolkitInterface
{
    protected function ileaPlusReportClient()
    {
        return new Client(['base_uri' => config('test_tools.tools.ilea_plus.report_base_url')]);
    }

    protected function ileaPlusClient()
    {
        return new Client(['base_uri' => config('test_tools.tools.ilea_plus.base_url') . '/client/', 'headers' => ['AUTHKEY' => config('test_tools.tools.ilea_plus.apiKey')]]);
    }

    public function getExamLoginUrl(Exam $exam)
    {
        $pass = decodeTanJob::decode(json_decode($exam->pivot->login_data)->tan);

        return config('test_tools.tools')[$exam->tool]['login_url'] . $exam->school_key . '/c/' . urlencode($pass);
    }

    public function getExamStatus(Exam $exam)
    {
        $status = getExamStatusJob::get($exam);

        if ($status !== 100) {
            $status = $this->setExamStatus($exam);
        }

        return $status;
    }

    public function setExamStatus(Exam $exam)
    {
        try {
            $students = json_decode($this->ileaPlusClient()->get('exams/' . $exam->exam_id . '/status')->getBody());
        } catch (\Exception $e) {
            logger()->error($e->getMessage());
            return new Response($e->getMessage(), $e->getCode());
        }

        count($students) === 0 ?: updateUsersCompletedExamJob::update($students, $exam);

        $status = getExamStatusJob::get($exam);

        setExamStatusJob::set($exam, $status);

        return $status;
    }

    public function getTests()
    {
        try {
            $tests = json_decode($this->ileaPlusClient()->get('tests')->getBody());
        } catch (\Exception $e) {
            logger()->error($e->getMessage());
            return new Response($e->getMessage(), $e->getCode());
        }

        foreach ($tests as $test) {
            $test->name = 'Ilea Plus' . ' - ' . $test->nameLong;
            $test->tool = 'ilea_plus';
        }

        return $tests;
    }

    public function createExam(int $group_id, int $test_id, string $test_name)
    {
        $school_key = Group::findOrFail($group_id)->organization->common_name;

        $users_ids = getStudentUsersInGroupJob::get($group_id);

        try {
            $test = json_decode($this->ileaPlusClient()->post('exams', [
                'json' => [
                    'testId' => $test_id,
                    'students' => $users_ids,
                    'schoolKey' => $school_key
                ]
            ])->getBody(), true);

        } catch (\Exception $e) {
            logger()->error($e->getMessage());
            return new Response($e->getMessage(), $e->getCode());
        }

        $exam = createExamJob::run(
            'ilea_plus',
            (string)$test['exam']['id'],
            $test_name,
            $school_key,
            ($test['exam']['subject'] === '1') ? "Mathematik" : "Deutsch",
            $school_key . '_TUT',
            Auth()->id(),
            $group_id
        );

        $this->addUsers($exam, $users_ids);

        return $exam;
    }

    public function deleteExam(Exam $exam): Response
    {
        try {
            $this->ileaPlusClient()->delete('exams/' . $exam->exam_id);
        } catch (\Exception $e) {
            logger()->error($e->getMessage());
            return new Response($e->getMessage(), $e->getCode());
        }

        $exam->delete();
        return new Response('Successfully removed exam', Response::HTTP_OK);
    }

    public function addUsers(Exam $exam, array $user_ids)
    {
        try {
            $studentLogins = json_decode($this->ileaPlusClient()->post('exams/users', [
                'json' => [
                    'examId' => $exam->exam_id,
                    'students' => $user_ids,
                ]
            ])->getBody(), true);
        } catch (\Exception $e) {
            logger()->error($e->getMessage());
            return new Response($e->getMessage(), $e->getCode());
        }

        $studentsData = getStudentsDataJob::toJson($studentLogins['student_logins']);

        $exam->users()->syncWithoutDetaching($studentsData);
        $exam->save();

        setExamStatusJob::set($exam, getExamStatusJob::get($exam));

        return new Response('Successfully added users to exam', Response::HTTP_CREATED);
    }

    public function removeUsers(Exam $exam, array $user_ids)
    {
        $this->getExamStatus($exam);

        $students_data = getUsersIleaIdsJob::run($exam->id, $user_ids);

        $user_ids = $students_data['user_ids'];

        try {
            $this->ileaPlusClient()->delete('exams/users', [
                'json' => [
                    'examId' => $exam->exam_id,
                    'students' => $students_data['login_data'],
                ]
            ]);
        } catch (\Exception $e) {
            logger()->error($e->getMessage());
            return new Response($e->getMessage(), $e->getCode());
        }

        $exam->users()->detach($user_ids);
        $exam->save();

        setExamStatusJob::set($exam, getExamStatusJob::get($exam));

        return new Response('Successfully removed users from exam', Response::HTTP_OK);
    }

    public function getReport(Exam $exam)
    {
        $studentsData = getStudentsDataJob::toArray($exam->id);

        try {
            $response = $this->ileaPlusReportClient()->post(
                '/ileaplus/pdf/' . $exam->exam_id, [
                'json' => $studentsData
            ]);
        } catch (\Exception $e) {
            logger()->error($e->getMessage());
            return new Response($e->getMessage(), $e->getCode());
        }

        return response($response->getBody(), 200)
            ->header('Content-Disposition', 'attachment; filename=' . $exam->test_name . '.pdf')
            ->header('Content-Type', 'application/pdf');
    }

}

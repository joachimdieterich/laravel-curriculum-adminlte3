<?php

namespace App\Http\Controllers\Tests;

use App\Domains\Exams\Jobs\getExamsJob;
use App\Domains\Exams\Models\Exam;
use App\Domains\Exams\Requests\ExamAddUsersRequest;
use App\Domains\Exams\Requests\ExamCreateRequest;
use App\Domains\Exams\Requests\ExamListRequest;
use App\Domains\Exams\Requests\ExamRemoveUsersRequest;
use App\Domains\Tests\Interfaces\TestToolkitInterface;
use App\Group;
use App\Http\Controllers\Controller;
use Yajra\DataTables\DataTables;

class ExamController extends Controller
{
    public function authUserIndexExams()
    {
        abort_unless(\Gate::allows('assignment_show'), 403);
        $exams = auth()->user()->exams;

        foreach ($exams as $exam) {
            $this->getToolExamStatus($exam);
        }

        $exams = auth()->user()->fresh()->exams;
        foreach($exams as $exam){
            $exam->login_url = config('test_tools.tools')[$exam->tool]['adapter']->getExamLoginUrl($exam);
        }

        return view('exams.index')
            ->with(compact('exams'));
    }

    public function index(ExamListRequest $examListRequest)
    {
        return getExamsJob::getFilteredExams($examListRequest);
    }

    public function show(Exam $exam)
    {
        abort_unless(\Gate::allows('test_access'), 403);

        return view('exams.show')
            ->with(compact('exam'));
    }

    public function create(ExamCreateRequest $examCreateRequest, TestToolkitInterface $testToolkit)
    {
        return $testToolkit->createExam($examCreateRequest->group_id, $examCreateRequest->test_id, $examCreateRequest->test_name);
    }

    public function delete(Exam $exam, TestToolkitInterface $testToolkit)
    {
        return $testToolkit->deleteExam($exam);
    }

    public function getExamStatus(Exam $exam, TestToolkitInterface $testToolkit)
    {
        return $testToolkit->getExamStatus($exam);
    }

    public function listExamUsers(Exam $exam)
    {
        $this->getToolExamStatus($exam);
        $users = $exam->users()->get()->toArray();

        return DataTables::of($users)
            ->addColumn('check', '')
            ->setRowId('id')
            ->make(true);
    }

    public function listAllUsers(Exam $exam)
    {
        $users = Group::where('id', $exam->group_id)->get()->first()->users()->whereDoesntHave('exams', function ($query) use ($exam) {
            $query->where('id', "=", $exam->id);
        });

        return DataTables::of($users)
            ->addColumn('check', '')
            ->setRowId('user_id') // changed from id to user_id
            ->make(true);
    }

    public function addUsers(ExamAddUsersRequest $examRemoveUsersRequest, Exam $exam, TestToolkitInterface $testToolkit)
    {
        return $testToolkit->addUsers($exam, $examRemoveUsersRequest->enrollment_list);
    }

    public function removeUsers(ExamRemoveUsersRequest $examRemoveUsersRequest, Exam $exam, TestToolkitInterface $testToolkit)
    {
        return $testToolkit->removeUsers($exam, $examRemoveUsersRequest->expel_list);
    }

    public function getReport(Exam $exam, TestToolkitInterface $testToolkit)
    {
        return $testToolkit->getReport($exam);
    }

    protected function getToolExamStatus(Exam $exam)
    {
        config('test_tools.tools')[$exam->tool]['adapter']->getExamStatus($exam);
    }

}

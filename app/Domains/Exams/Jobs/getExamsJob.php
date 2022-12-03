<?php

namespace App\Domains\Exams\Jobs;

use App\Domains\Exams\Models\Exam;
use App\Domains\Exams\Requests\ExamListRequest;
use JamesDordoy\LaravelVueDatatable\Http\Resources\DataTableCollectionResource;

class getExamsJob
{
    public static function getFilteredExams(ExamListRequest $examListRequest)
    {
        $exams = Exam::eloquentQuery($examListRequest->column, $examListRequest->dir, '')
            ->where('group_id', $examListRequest->group_id)
            ->where(function ($q) use ($examListRequest) {
                $q->orWhere('tool', 'like', "%$examListRequest->search%")
                    ->orWhere('test_name', 'like', "%$examListRequest->search%");
            })->paginate($examListRequest->length);

        return new DataTableCollectionResource($exams);
    }
}

<?php

namespace App\Http\Controllers;

use App\CurriculumType;
use Illuminate\Http\JsonResponse;

class CurriculumTypeController extends Controller
{
    public function index(): JsonResponse
    {
        return getEntriesForSelect2ByCollectionAlternative(
            CurriculumType::all()->map(function ($curriculumType) {
                $curriculumType->title = trans('global.curriculumType.' . $curriculumType->title);

                return $curriculumType;
            })
        );
    }
}

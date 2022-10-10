<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Grade;
use App\Http\Controllers\Controller;

class GradesApiController extends Controller
{
    public function index()
    {
        $grades = Grade::all();

        return $grades;
    }

    public function show(Grade $grade)
    {
        return $grade;
    }
}

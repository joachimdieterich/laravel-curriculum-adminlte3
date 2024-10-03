<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Grade;
use App\Http\Controllers\Controller;

class GradesApiController extends Controller
{
    public function index()
    {
        return Grade::all();
    }

    public function show(Grade $grade)
    {
        return $grade;
    }
}

<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Subject;

class SubjectsApiController extends Controller
{
    public function index()
    {
        return Subject::all();
    }

    public function show(Subject $subject)
    {
        return $subject;
    }
}

<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Subject;

class SubjectsApiController extends Controller {

    public function index() {
        $subjects = Subject::all();

        return $subjects;
    }

    public function show(Subject $subject) {

        return $subject;
    }

}

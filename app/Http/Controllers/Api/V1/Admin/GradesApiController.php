<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Grade;

class GradesApiController extends Controller {

    public function index() {
        $grades = Grade::all();

        return $grades;
    }

    public function show(Grade $grade) {

        return $grade;
    }

}

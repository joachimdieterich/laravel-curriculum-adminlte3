<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Period;

class PeriodsApiController extends Controller {

    public function index() {
        $periods = Period::all();

        return $periods;
    }

    public function show(Period $period) {

        return $period;
    }

}

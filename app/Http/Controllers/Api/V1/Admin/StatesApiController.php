<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\State;

class StatesApiController extends Controller {

    public function index() {
        $states = State::all();

        return $states;
    }

    public function show(State $state) {

        return $state;
    }

}

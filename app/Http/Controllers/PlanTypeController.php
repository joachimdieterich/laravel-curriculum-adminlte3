<?php

namespace App\Http\Controllers;

class PlanTypeController extends Controller
{
    public function index()
    {

        if (request()->wantsJson()) {
            return getEntriesForSelect2ByModel(
                "App\PlanType"
            );
        } else {
            abort(405);
        }
    }
}

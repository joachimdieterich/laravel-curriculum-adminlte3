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
        }/* else {
            abort_unless(\Gate::allows('plan_access'), 403);
            return view('plantypes.index');
        }*/
    }
}

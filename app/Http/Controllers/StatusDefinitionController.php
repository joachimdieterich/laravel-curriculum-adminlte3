<?php

namespace App\Http\Controllers;

use App\StatusDefinition;

class StatusDefinitionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return array
     */
    public function index()
    {
        $status_definitions = StatusDefinition::all();
        // axios call?
        if (request()->wantsJson()) {
            return [
                'message' => $status_definitions,
            ];
        }

        return $status_definitions;
    }
}

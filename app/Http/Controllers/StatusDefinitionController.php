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
        if (request()->wantsJson() AND request()->has(['term', 'page'])) {
            return getEntriesForSelect2ByModel("App\StatusDefinition", "lang_de", "lang_de", "lang_de", "status_definition_id");
        }


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

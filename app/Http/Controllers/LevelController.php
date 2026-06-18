<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class LevelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse|void
     */
    public function index()
    {
        if (request()->wantsJson()){
            return getEntriesForSelect2ByModel(
                "App\Level"
            );
        }
        //return Level::all()->toJson();
    }
}

<?php

namespace App\Http\Controllers;

use App\Country;

class CountryController extends Controller
{
    public function index()
    {
        if (request()->wantsJson()) {
            return Country::select('*')->with(['states'])->get();
        }
    }

    public function show(Country $country)
    {
        if (request()->wantsJson()) {
            return $country->states()->get();
        }
    }

    public function getStates(Country $country)
    {
        return $country->states()->get();
    }
}

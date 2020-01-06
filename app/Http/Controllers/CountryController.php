<?php

namespace App\Http\Controllers;

use App\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    public function getStates(Country $country)
    {
        return $country->states()->get();
    }
}

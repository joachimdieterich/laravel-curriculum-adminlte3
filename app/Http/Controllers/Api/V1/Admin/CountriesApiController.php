<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Country;
use App\Http\Controllers\Controller;

class CountriesApiController extends Controller
{
    public function index()
    {
        return Country::all();
    }

    public function show(Country $country)
    {
        return $country;
    }
}

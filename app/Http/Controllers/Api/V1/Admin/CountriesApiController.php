<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Country;

class CountriesApiController extends Controller
{
    public function index()
    {
        $countries = Country::all();

        return $countries;
    }


    public function show(Country $country)
    {

        return $country;
        
    }

   
}

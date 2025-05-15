<?php

namespace App\Http\Controllers;

use App\Country;

class CountryController extends Controller
{
    public function index()
    {
        if (request()->wantsJson()) {
            return getEntriesForSelect2ByModel("App\Country", 'lang_de', 'lang_de', 'lang_de', 'alpha2');
        }
    }

    public function show(Country $country)
    {

    /*    if (request()->wantsJson()) {
            return getEntriesForSelect2ByModel("App\State", 'lang_de', 'lang_de', 'lang_de', 'code');
        }*/
        if (request()->wantsJson()) {
            return $country->states()->get();
        }
    }

    public function getStates(Country $country)
    {
        if (request()->wantsJson()) {
            return getEntriesForSelect2ByModel("App\State", 'code', 'lang_de', 'lang_de', 'code');
        }
    }
}

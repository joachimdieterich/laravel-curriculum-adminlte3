<?php

namespace App\Http\Controllers\API\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;



class AboutApiController extends Controller
{
    
    public function index()
    {
        return "Curriculum API V1 (about) works";
    }

}

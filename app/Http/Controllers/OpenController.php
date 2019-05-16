<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Content;

class OpenController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        
    }

    public function impressum()
    {   
        $impressum = Content::where('title', '=', 'Impressum')->first();
        
        return view('impressum', compact('impressum'));
    }
    
    public function terms()
    {   
        $terms = Content::where('title', '=', 'Terms')->first();
        
        return view('terms', compact('terms'));
    }
}

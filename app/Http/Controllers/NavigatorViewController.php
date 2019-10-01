<?php

namespace App\Http\Controllers;

use App\Navigator;
use App\NavigatorView;
use Illuminate\Http\Request;

use File;

class NavigatorViewController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\NavigatorView  $navigatorView
     * @return \Illuminate\Http\Response
     */
    public function show(Navigator $navigator, NavigatorView $navigator_view)
    {
         $files = File::allFiles(storage_path("app/subjects"));
        //dd($files);
        foreach ($files as $value) 
        {
            
           // dd($value);
            
            
        }
        //dd([$navigator, $navigator_view]);
        $navigators = Navigator::where('id', $navigator->id)->get()->first();
        $views      = NavigatorView::where('id', $navigator_view->id)
                                    ->where('navigator_id', $navigator->id)
                                    ->with(['items'])
                                    ->get()->first();

        //dd($views);                                        
        return view('navigators.show')
                ->with(compact('navigators'))
                ->with(compact('views'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\NavigatorView  $navigatorView
     * @return \Illuminate\Http\Response
     */
    public function edit(NavigatorView $navigatorView)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\NavigatorView  $navigatorView
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, NavigatorView $navigatorView)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\NavigatorView  $navigatorView
     * @return \Illuminate\Http\Response
     */
    public function destroy(NavigatorView $navigatorView)
    {
        //
    }
}

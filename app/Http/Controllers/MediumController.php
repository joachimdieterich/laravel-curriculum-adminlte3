<?php

namespace App\Http\Controllers;

use App\Medium;
use File;
use Illuminate\Http\Request;
use Laravolt\Avatar\Avatar;

class MediumController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $media = Medium::all();
        
        return compact('media');
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
        return Medium::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Medium  $medium
     * @return \Illuminate\Http\Response
     */
    public function show(Medium $medium)
    {
        $base_path = config('lfm.files_folder_name')."/".auth()->user()->id; //define path to current users folder
        /* id link */
        if ($medium->mime_type == 'url'){
            return redirect($medium->path);
        }
        
        $path = storage_path('app'.$medium->path.$medium->medium_name);
        //dd($path);
        if (!file_exists($path)) {
            abort(404);
        }
        
        /* if subscribed */
            /* check visibility */

            /* check sharing level */
        /* end if subscribed */
        
        // file not found

//        $pdfContent = Storage::get($path);
//
//        // for pdf, it will be 'application/pdf'
//        $type       = Storage::mimeType($path);
//        $fileName   = Storage::name($path);
//
//        return Response::make($pdfContent, 200, [
//          'Content-Type'        => $type,
//          'Content-Disposition' => 'inline; filename="'.$fileName.'"'
//        ]);
        
        return response()->file($path);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Medium  $medium
     * @return \Illuminate\Http\Response
     */
    public function edit(Medium $medium)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Medium  $medium
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Medium $medium)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Medium  $medium
     * @return \Illuminate\Http\Response
     */
    public function destroy(Medium $medium)
    {
        //
    }
    
    public function getMediumByEventPath($path)
    {
        $m = new Medium();
        return Medium::where('path', $m->convertFilemanagerEventPathToMediumPath($path))
                        ->where('medium_name', basename($path))
                        ->get()->first();
    }
}

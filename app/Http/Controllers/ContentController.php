<?php

namespace App\Http\Controllers;

use App\Content;
use Illuminate\Http\Request;
use \Barryvdh\Snappy\Facades\SnappyPdf;

class ContentController extends Controller
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
        
        return view('contents.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //persist
        $content = auth()->user()->contents()->create($this->validateRequest());
        
        //redirect
        return redirect($content->path());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Content  $content
     * @return \Illuminate\Http\Response
     */
    public function show(Content $content)
    {
       // dd($content);//
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Content  $content
     * @return \Illuminate\Http\Response
     */
    public function edit(Content $content)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Content  $content
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Content $content)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Content  $content
     * @return \Illuminate\Http\Response
     */
    public function destroy(Content $content)
    {
        //
    }
    
    protected function validateRequest(){
        return request()->validate([
            'title' => 'sometimes',
            'content' => 'sometimes|required',
            ]);
    }
    
    public function print(Content $content)
    {
        $content->content = relativeToAbsoutePaths($content->content);
        $meta = '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';
        $pdf = SnappyPdf::loadHTML("{$meta}<h1>{$content->title}</h1>{$content->content}")
            ->setPaper('a4')
            ->setOption('margin-bottom', 0);
        return $pdf->download("{$content->title}.pdf");
            
    }  
    
}

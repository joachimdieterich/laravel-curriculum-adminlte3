<?php

namespace App\Http\Controllers;

use App\ContentSubscription;
use App\Glossar;
use Illuminate\Http\Request;

class GlossarController extends Controller
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
     * @param  \App\Glossar  $glossar
     * @return \Illuminate\Http\Response
     */
    public function show(Glossar $glossar)
    {
        $subscriptions = ContentSubscription::where([
            'subscribable_type' => "App\Glossar",
            'subscribable_id'   => $glossar->id
        ])->orderBy('order_id');


        if (request()->wantsJson()){
            return ['message' => $subscriptions->with(['content'])->get()];
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Glossar  $glossar
     * @return \Illuminate\Http\Response
     */
    public function edit(Glossar $glossar)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Glossar  $glossar
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Glossar $glossar)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Glossar  $glossar
     * @return \Illuminate\Http\Response
     */
    public function destroy(Glossar $glossar)
    {
        abort_unless(\Gate::allows('curriculum_delete'), 403);
        // delete contents
        foreach ($glossar->contents AS $content)
        {
            (new ContentController)->destroy($content); // delete or unsubscribe if content is still subscribed elsewhere
        }

        return $glossar->delete();
    }
}

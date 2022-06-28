<?php

namespace App\Http\Controllers;

use App\ContentSubscription;
use App\Glossar;
use Illuminate\Http\Request;

class GlossarController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        return $this->store($request);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        abort_unless(\Gate::allows('glossar_create'), 403);

        $new_glossar = $this->validateRequest();

        Glossar::create([
            'subscribable_type' => $new_glossar['subscribable_type'],
            'subscribable_id' => $new_glossar['subscribable_id'],
        ]);

        return back();
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
            'subscribable_id'   => $glossar->id,
        ])->with(['content'])->get(); //todo: sort by content

        if (request()->wantsJson()) {
            return ['message' => $subscriptions];
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Glossar  $glossar
     * @return \Illuminate\Http\Response
     */
    public function destroy(Glossar $glossar)
    {
        abort_unless(\Gate::allows('glossar_delete'), 403);
        // delete contents
        foreach ($glossar->contents as $content) {
            (new ContentController)->destroy($content, 'App\Glossar', $glossar->id); // delete or unsubscribe if content is still subscribed elsewhere
        }

        if (request()->wantsJson()) {
            return ['message' => $glossar->delete()];
        }
    }

    protected function validateRequest()
    {
        return request()->validate([
            'subscribable_type'       => 'required',
            'subscribable_id'         => 'required',
        ]);
    }
}

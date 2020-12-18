<?php

namespace App\Http\Controllers;

use App\Content;
use App\ContentSubscription;

use Illuminate\Http\Request;
use \Barryvdh\Snappy\Facades\SnappyPdf;
use Illuminate\Support\Facades\DB;

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
        abort_unless(\Gate::allows('content_create'), 403);
        //persist
        $input = $this->validateRequest();
        $content = Content::Create([
            'title' => $input['title'],
            'content' => $input['content'],
            'owner_id'  => auth()->user()->id,
        ]);

        //subscribe to model
        if (isset($input['referenceable_type']) AND isset($input['referenceable_id'])){
            $model = $input['referenceable_type']::find($input['referenceable_id']);
            $content->subscribe($model);
        }
        $content->categories()->attach($input['categorie_ids']);

        // axios call?
        if (request()->wantsJson()){
            return ['message' => $content];
        }
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
       if (request()->wantsJson()){
            return [
                'message' => $content
            ];
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Content  $content
     * @return \Illuminate\Http\Response
     */
    public function edit(Content $content)
    {

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
        abort_unless(\Gate::allows('content_edit'), 403);

        $input = $this->validateRequest();
        $content->update([
            'title' => $input['title'],
            'content' => $input['content'],
            'owner_id'  => auth()->user()->id,
        ]);
        $content->categories()->sync($input['categorie_ids']);

        if (request()->wantsJson()){
            return ['message' => $content];
        }

        //$content->categories()->sync($input['categorie_ids']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Content  $content
     * @return \Illuminate\Http\Response
     */
    public function destroy(Content $content, $subscribable_type = null, $subscribable_id = null )
    {
        abort_unless(\Gate::allows('content_delete'), 403);
        /**
         * check if content is subscribed only by deleting reference
         * - if yes -> delete content_subscription and content
         * - if not -> delete only content_subscription
         */

        $input = $this->validateRequest();
        if (isset($input['referenceable_id']) AND isset($input['referenceable_type'])){
            $subscribable_type = $input['referenceable_type'];
            $subscribable_id   = $input['referenceable_id'];
        }

        if ($content->subscriptions()->count() <= 1){

            ContentSubscription::where('subscribable_type',
                    (isset(request('subscribable')['content_subscriptions'][0]['subscribable_type'])) ? request('subscribable')['content_subscriptions'][0]['subscribable_type'] : $subscribable_type)
                ->where('subscribable_id', (isset(request('subscribable')['id'])) ? request('subscribable')['id'] : $subscribable_id)
                ->where('content_id', $content->id)
                ->delete();

            // delete contents
            foreach ($content->quotes AS $quote)
            {
                (new QuoteController)->destroy($quote); // delete and unsubscribe related objects
            }

            //todo? delete unused categorie_categoie
            $content->delete();

        } else {
            $subscription = ContentSubscription::where('subscribable_type', $subscribable_type)
                ->where('subscribable_id', $subscribable_id)
                ->where('content_id', $content->id)->get()->first(); //load subscription to get order_id for reordering
            ContentSubscription::where('subscribable_type',
                (isset(request('subscribable')['content_subscriptions'][0]['subscribable_type'])) ? request('subscribable')['content_subscriptions'][0]['subscribable_type'] : $subscribable_type)
                ->where('subscribable_id',(isset(request('subscribable')['id'])) ? request('subscribable')['id'] : $subscribable_id)
                ->where('content_id', $content->id)
                ->delete();
            //reset order_ids
            return (new ContentSubscription)
                ->where('subscribable_type', $subscribable_type)
                ->where('subscribable_id', $subscribable_id)
                ->where('order_id', '>', $subscription->order_id)
                ->update([
                    'order_id'=> DB::raw('order_id -1')
                ]);
        }
        // axios call?
        if (request()->wantsJson()){
            return ['message' => true];
        }
    }

    protected function validateRequest(){
        return request()->validate([
            'title' => 'sometimes',
            'content' => 'sometimes|required',
            'categorie_ids' => 'sometimes',
            'referenceable_id' => 'sometimes',
            'referenceable_type' => 'sometimes',

            ]);
    }

}

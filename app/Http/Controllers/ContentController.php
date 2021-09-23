<?php

namespace App\Http\Controllers;

use App\Content;
use App\ContentSubscription;

use App\Medium;
use App\MediumSubscription;
use Illuminate\Http\Request;
use \Barryvdh\Snappy\Facades\SnappyPdf;
use Illuminate\Support\Facades\DB;

class ContentController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $this->validateRequest();
        abort_unless((\Gate::allows('content_create') OR
            \Gate::allows($input['referenceable_type'].'_content_create')), 403);

        $this->permissionCheck($input['referenceable_type'], $input['referenceable_id']); //check context permission

        $content = Content::Create([
            'title' => $input['title'],
            'content' => $input['content'],
            'owner_id'  => auth()->user()->id,
        ]);
        // subscribe embedded media to content
        $this->checkForEmbeddedMedia($content);

        //subscribe to model
        if (isset($input['referenceable_type']) and isset($input['referenceable_id']))
        {
            $model = $input['referenceable_type']::find($input['referenceable_id']);
            $content->subscribe($model);
        }

        // not used -> see github issue: Remove category from ContentCreateModal (not used) #210
        // $content->categories()->attach($input['categorie_ids']);

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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Content  $content
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Content $content)
    {
        $input = $this->validateRequest();

        abort_unless((\Gate::allows('content_edit') OR
            \Gate::allows($input['referenceable_type'].'_content_edit')), 403);
        //todo: check if user is owner or has creator/admin role

        $content->update([
            'title' => $input['title'],
            'content' => $input['content'],
            'owner_id'  => auth()->user()->id,
        ]);
        // subscribe embedded media to content
        $this->checkForEmbeddedMedia($content);

        // not used -> see github issue: Remove category from ContentCreateModal (not used) #210
        // $content->categories()->attach($input['categorie_ids']);

        if (request()->wantsJson()){
            return ['message' => $content];
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Content  $content
     * @return \Illuminate\Http\Response
     */
    public function destroy(Content $content, $subscribable_type = null, $subscribable_id = null )
    {
        $input = $this->validateRequest();
        if (isset($input['referenceable_id']) AND isset($input['referenceable_type'])){
            $subscribable_type = $input['referenceable_type'];
            $subscribable_id   = $input['referenceable_id'];
        }

        abort_unless((\Gate::allows('content_delete') OR
            \Gate::allows($subscribable_type.'_content_delete')), 403);

        //todo: check if user is owner or has creator/admin role
        /**
         * check if content is subscribed only by deleting reference
         * - if yes -> delete content_subscription and content
         * - if not -> delete only content_subscription
         */

        //delete unused embedded media
        $media = $content->media;

        $content->mediaSubscriptions()
            ->where('subscribable_type', '=', 'App\Content')
            ->where('subscribable_id', '=', $content->id)
            ->delete();

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

        //delete unused media
        foreach ($media AS $medium)
        {
            Medium::where('id', $medium->id)->delete();
        }
        // axios call?
        if (request()->wantsJson()){
            return ['message' => true];
        }
    }

    protected function checkForEmbeddedMedia($content)
    {
        preg_match_all('/src="\\/media\\/(.+?)"/s', $content->content, $matches, PREG_SET_ORDER, 0);
        foreach ($matches as $match)
        {
            $this->subscribeMediaToModel($content,  Medium::find($match[1]));
        }
    }

    private function subscribeMediaToModel($model, $medium)
    {
            $subscribe = MediumSubscription::updateOrCreate([
                "medium_id"         => $medium->id,
                "subscribable_type" => get_class($model),
                "subscribable_id"   => $model->id,
            ],[
                "sharing_level_id"  => 1, // has to be global = 1
                "visibility"        => 1, // has to be public  = 1
                "owner_id"          => auth()->user()->id,
            ]);
            $subscribe->save();
    }

    protected function validateRequest()
    {
        return request()->validate([
            'title' => 'sometimes',
            'content' => 'sometimes|required',
            'categorie_ids' => 'sometimes',
            'referenceable_id' => 'sometimes',
            'referenceable_type' => 'sometimes',
        ]);
    }

    /**
     * check if user is owner of curricula if creation context is curricula
     *
     * @param $referenceable_type
     * @param $referenceable_id
     * @return mixed
     */
    private function permissionCheck($referenceable_type, $referenceable_id)
    {

        if (in_array(
            $referenceable_type . '_content_create',
            [
                "App\Curriculum_content_create",
                "App\EnablingObjective_content_create",
                "App\TerminalObjective_content_create",
                "App\LogbookEntry_content_create",
            ]
            )
        ) {
            $model = $referenceable_type::find($referenceable_id);

            switch ($referenceable_type) {
                case "App\Curriculum":
                case "App\LogbookEntry":
                    abort_unless(($model->owner_id === auth()->user()->id), 403);
                    break;
                case "App\EnablingObjective":
                case "App\TerminalObjective":
                    abort_unless(($model->curriculum->owner_id === auth()->user()->id), 403);
                    break;
            }

        }
        return $model;
    }

}

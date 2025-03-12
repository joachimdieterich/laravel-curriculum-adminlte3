<?php

namespace App\Http\Controllers;

use App\Content;
use App\ContentSubscription;
use App\Medium;
use App\MediumSubscription;
use Illuminate\Http\Request;
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
        $this->permissionCheck($input['subscribable_type'], $input['subscribable_id']); //check context permission

        $content = Content::Create([
            'title' => $input['title'],
            'content' => $input['content'],
            'owner_id'  => auth()->user()->id,
        ]);
        // subscribe embedded media to content
        $this->checkForEmbeddedMedia($content);

        //subscribe to model
        if (isset($input['subscribable_type']) and isset($input['subscribable_id'])) {
            $model = $input['subscribable_type']::find($input['subscribable_id']);
            $subscription = $content->subscribe($model)->toArray();
            $subscription['content'] = $content;

            return $subscription;
        }

        if (request()->wantsJson()) {
            return $content;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Content  $content
     * @return \Illuminate\Http\Response
     */
    public function show(Content $content)
    {
        abort_unless($content->isAccessible(), 403);

        if (request()->wantsJson()) {
            return $content;
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

        $this->permissionCheck($input['subscribable_type'], $input['subscribable_id'], 'edit');

        $content->update([
            'title' => $input['title'],
            'content' => $input['content'],
            'owner_id' => auth()->user()->id,
        ]);
        // subscribe embedded media to content
        $this->checkForEmbeddedMedia($content);

        if (request()->wantsJson()) {
            return $content;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Content  $content
     * @return \Illuminate\Http\Response
     */
    public function destroy(Content $content, $subscribable_type = null, $subscribable_id = null)
    {
        $input = $this->validateRequest();

        if (isset($input['subscribable_id']) and isset($input['subscribable_type'])) {
            $subscribable_type = $input['subscribable_type'];
            $subscribable_id = $input['subscribable_id'];
        }

        $this->permissionCheck($subscribable_type, $subscribable_id, 'delete');

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

        if ($content->subscriptions()->count() <= 1) {
            ContentSubscription::where('subscribable_type',
                    (isset(request('subscribable')['content_subscriptions'][0]['subscribable_type'])) ? request('subscribable')['content_subscriptions'][0]['subscribable_type'] : $subscribable_type)
                ->where('subscribable_id', (isset(request('subscribable')['id'])) ? request('subscribable')['id'] : $subscribable_id)
                ->where('content_id', $content->id)
                ->delete();

            // delete contents
            foreach ($content->quotes as $quote) {
                (new QuoteController)->destroy($quote); // delete and unsubscribe related objects
            }

            //todo? delete unused categories, not used anymore?
            $content->delete();
        } else {
            $subscription = ContentSubscription::where('subscribable_type', $subscribable_type)
                ->where('subscribable_id', $subscribable_id)
                ->where('content_id', $content->id)->get()->first(); //load subscription to get order_id for reordering
            ContentSubscription::where('subscribable_type',
                (isset(request('subscribable')['content_subscriptions'][0]['subscribable_type'])) ? request('subscribable')['content_subscriptions'][0]['subscribable_type'] : $subscribable_type)
                ->where('subscribable_id', (isset(request('subscribable')['id'])) ? request('subscribable')['id'] : $subscribable_id)
                ->where('content_id', $content->id)
                ->delete();
            //reset order_ids
            return (new ContentSubscription)
                ->where('subscribable_type', $subscribable_type)
                ->where('subscribable_id', $subscribable_id)
                ->where('order_id', '>', $subscription->order_id)
                ->update([
                    'order_id'=> DB::raw('order_id -1'),
                ]);
        }

        //delete unused media
        foreach ($media as $medium) {
            Medium::where('id', $medium->id)->delete();
        }
        // axios call?
        if (request()->wantsJson()) {
            return ['message' => true];
        }
    }

    protected function checkForEmbeddedMedia($content)
    {
        preg_match_all('/src="\\/media\\/(.+?)"/s', $content->content, $matches, PREG_SET_ORDER, 0);
        foreach ($matches as $match) {
            $this->subscribeMediaToModel($content, Medium::find($match[1]));
        }
    }

    private function subscribeMediaToModel($model, $medium)
    {
        $subscribe = MediumSubscription::updateOrCreate([
            'medium_id' => $medium->id,
            'subscribable_type' => get_class($model),
            'subscribable_id' => $model->id,
        ], [
            'sharing_level_id' => 1, // has to be global = 1
            'visibility' => 1, // has to be public  = 1
            'owner_id' => auth()->user()->id,
        ]);
        $subscribe->save();
    }

    protected function validateRequest()
    {
        return request()->validate([
            'title' => 'sometimes',
            'content' => 'sometimes|required',
            //'categorie_ids' => 'sometimes',
            'subscribable_id' => 'sometimes',
            'subscribable_type' => 'sometimes',
        ]);
    }

    /**
     * check if user is owner of curricula if creation context is curricula
     *
     * @param $subscribable_type
     * @param $subscribable_id
     * @return mixed
     */
    private function permissionCheck($subscribable_type, $subscribable_id, $action = 'create')
    {
        abort_unless((\Gate::allows('content_'.$action) or
            \Gate::allows($subscribable_type.'_content_'.$action)), 403);

        $model = $subscribable_type::find($subscribable_id);

        if (is_admin()) {        //admin can edit every model
            return $model;
        }

        switch ($subscribable_type) {
            case "App\Curriculum":
                abort_unless(($model->owner_id === auth()->user()->id), 403);
                break;
            case "App\LogbookEntry":
                abort_unless($model->isAccessible(), 403);
                break;
            case "App\EnablingObjective":
            case "App\TerminalObjective":
                abort_unless(($model->curriculum->owner_id === auth()->user()->id), 403);
                break;
        }

        return $model;
    }
}

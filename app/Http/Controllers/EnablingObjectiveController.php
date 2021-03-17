<?php

namespace App\Http\Controllers;


use App\Config;
use App\EnablingObjective;
use App\Group;
use App\Medium;
use App\User;
use Illuminate\Http\Request;
use App\Http\Requests\StoreEnablingObjectiveRequest;
use App\Http\Requests\UpdateEnablingObjectiveRequest;
use DB;
use App\ReferenceSubscription;
use App\QuoteSubscription;
use Illuminate\Support\Collection;

class EnablingObjectiveController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreEnablingObjectiveRequest $request)
    {

        $order_id = $this->getMaxOrderId(request('terminal_objective_id'));
        $enablingObjective = EnablingObjective::create(array_merge($request->all(), ['order_id' => $order_id]));

        if (request()->wantsJson()){
            return ['message' => $enablingObjective];
        }
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\EnablingObjective  $enablingObjective
     * @return \Illuminate\Http\Response
     */
    public function show(EnablingObjective $enablingObjective)
    {

        $objective = EnablingObjective::where('id', $enablingObjective->id)
            ->with(['curriculum', 'curriculum.subject', 'terminalObjective.type',
                    'referenceSubscriptions.siblings.referenceable', 'quoteSubscriptions.siblings.quotable'])
            ->get()->first();

        $repository = Config::where('key', 'repository')->get()->first();

        return view('objectives.show')
            ->with(compact('objective'))
            ->with(compact('repository'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\EnablingObjective  $enablingObjective
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateEnablingObjectiveRequest $request, EnablingObjective $enablingObjective)
    {
        //first get existing data to later adjust order_id
        $old_objective = EnablingObjective::find(request('id'));

        if ($request->has('order_id')){
            if (request()->wantsJson()){
                return ['message' => $this->toggleOrderId($old_objective, request('order_id')) ];
            }
        }
        if (request()->wantsJson()){
            return ['message' => $enablingObjective->update($request->all())];
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\EnablingObjective  $enablingObjective
     * @return \Illuminate\Http\Response
     */
    public function destroy(EnablingObjective $enablingObjective)
    {
        abort_unless(\Gate::allows('objective_delete'), 403);

        //set temp vars
        $curriculum_id          = $enablingObjective->curiculum_id;
        $terminal_objective_id  = $enablingObjective->terminal_objective_id;
        $order_id               = $enablingObjective->order_id;


        // delete contents
        foreach ($enablingObjective->contents AS $content)
        {
            (new ContentController)->destroy($content, 'App\EnablingObjective', $enablingObjective->id); // delete or unsubscribe if content is still subscribed elsewhere
        }
        // delete achievements
        $enablingObjective->achievements()
                ->where('referenceable_type', '=', 'App\EnablingObjective')
                ->where('referenceable_id', '=', $enablingObjective->id)
                ->delete();

        // delete subscriptions
        $enablingObjective->subscriptions()
                ->where('enabling_objective_id', '=', $enablingObjective->id)
                ->delete();

        // delete mediaSubscriptions
        $media = $enablingObjective->media;
        $enablingObjective->mediaSubscriptions()
                ->where('subscribable_type', '=', 'App\EnablingObjective')
                ->where('subscribable_id', '=', $enablingObjective->id)
                ->delete();

        // delete quoteSubscriptions/Quotes
        $enablingObjective->quoteSubscriptions()
                ->where('quotable_type', '=', 'App\EnablingObjective')
                ->where('quotable_id', '=', $enablingObjective->id)
                ->delete();

        // delete referenceSubscriptions
        $enablingObjective->referenceSubscriptions()
                ->where('referenceable_type', '=', 'App\EnablingObjective')
                ->where('referenceable_id', '=', $enablingObjective->id)
                ->delete();

        // delete repositorySubscriptions
        $enablingObjective->repositorySubscriptions()
                ->where('subscribable_type', '=', 'App\EnablingObjective')
                ->where('subscribable_id', '=', $enablingObjective->id)
                ->delete();
        // delete prerequisites entries (predecessors)
        $enablingObjective->predecessors()
            ->where('successor_type', '=', 'App\EnablingObjective')
            ->where('successor_id', '=', $enablingObjective->id)
            ->delete();
        // delete prerequisites entries (successors)
        $enablingObjective->successors()
            ->where('predecessor_type', '=', 'App\EnablingObjective')
            ->where('predecessor_id', '=', $enablingObjective->id)
            ->delete();

        //delete objective
        $return = $enablingObjective->delete();

        //reset order_ids
        $this->resetOrderIds($curriculum_id, $terminal_objective_id, $order_id);

        //delete unused media
        foreach ($media AS $medium)
        {
            Medium::where('id', $medium->id)->delete();
        }

        if (request()->wantsJson()){
            return ['message' => $return];
        }
        //return $return;
    }


    public function referenceSubscriptionSiblings(EnablingObjective $enablingObjective)
    {
        $siblings = new Collection([]);

        foreach ($enablingObjective->referenceSubscriptions as $referenceSubscription)
        {

             $collection = ReferenceSubscription::where('reference_id', '=', $referenceSubscription->reference_id)
                                ->where(function($query) use ($referenceSubscription, $enablingObjective)
                                    {
                                        return $query->where('reference_id', '=', $referenceSubscription->reference_id)
//                                                      ->where('referenceable_type', '!=','App\EnablingObjective')
                                                      ->where('referenceable_id', '!=', $enablingObjective->id);
                                    })
                                ->with(['referenceable.curriculum.organizationType'])
                                ->with(['reference'])
                     ->get();
            $siblings= $siblings->merge($collection);
        }

        if (count($siblings) == 0) //end early
        {
            return ['message'=> 'no subscriptions'];
        }

        foreach ($siblings as $sibling)
        {
            $curricula_list[$sibling->referenceable->curriculum->id] = $sibling->referenceable->curriculum;
        }
        return ['siblings' => $siblings, 'curricula_list' => $curricula_list];
    }

    public function quoteSubscriptions(EnablingObjective $enablingObjective)
    {
        $collection = QuoteSubscription::where('quotable_id', '=', $enablingObjective->id)
                                        ->where('quotable_type', '=', 'App\EnablingObjective')
                                        ->with(['quote.content.subscriptions.subscribable'])
                                        ->get();

        if (count($collection) == 0) //end early
        {
            return ['message'=> 'no subscriptions'];
        }

        foreach ($collection as $quote_subscriptions)
        {
            $arr[$quote_subscriptions->quote_id] = !is_null($quote_subscriptions->quote);

            if (!is_null($quote_subscriptions->quote))
            {

                $curricula_list[$quote_subscriptions->quote->content->subscriptions[0]->subscribable->id] = $quote_subscriptions->quote->content->subscriptions[0]->subscribable;
                $quotes_subscriptions[] = $quote_subscriptions;
            }

        }

        return ['quotes_subscriptions' => $quotes_subscriptions, 'curricula_list' => $curricula_list];

    }

    protected function getMaxOrderId($terminal_objective_id)
    {
        $order_id = DB::table('enabling_objectives')
                                ->where('terminal_objective_id', $terminal_objective_id)
                                ->max('order_id');

        return (is_numeric($order_id)) ? $order_id + 1 : 0 ;

    }

    protected function resetOrderIds($curriculum_id, $terminal_objective_id, $order_id, $direction = 'down')
    {
        return (new EnablingObjective)->where('curriculum_id', $curriculum_id)
                                        ->where('terminal_objective_id', $terminal_objective_id)
                                        ->where('order_id', '>', $order_id)
                                        ->update([
                                           'order_id'=> DB::raw('order_id'. ( ($direction === 'down') ? '-1' : '+1') )
                                        ]);
    }

    /**
     *
     * @param int $curriculum_id
     * @param int $order_id
     * @param int $new_order_id
     * @return type
     */
    protected function toggleOrderId($old_objective, $new_order_id)
    {
        // toggle order_ids of terminal objectives
        $responseA = (new EnablingObjective)->where('curriculum_id', $old_objective->curriculum_id)
                            ->where('terminal_objective_id', $old_objective->terminal_objective_id)
                            ->where('order_id', '=', $new_order_id)
                            ->update([ 'order_id'=> $old_objective->order_id ]);

        $responseB = (new EnablingObjective)->where('id', $old_objective->id)
                                ->update([ 'order_id'=> $new_order_id]);

        if (($responseA == true) AND ($responseB == true))
        {
            return '/curricula/'.$old_objective->curriculum_id;
        }

    }

    /**
     * Display the specified resource with achievements.
     *
     * @param  \App\EnablingObjective  $enablingObjective
     * @return array
     */
    public function showAchievements(EnablingObjective $enablingObjective, $group = null)
    {

        if ($group == 'null')
        {
            $user_ids =  [ auth()->user()->id ];
        }
        else
        {
            $user_ids = Group::find($group)->users()->get()->pluck('id');
        }

        $result = ['objective' => EnablingObjective::with(
                    ['achievements' => function($query) use ($user_ids)
                        {
                            $query->whereIn('user_id', $user_ids)->with(['owner', 'user']);
                        }
                    ])->find($enablingObjective->id),
                    'users' => User::select([
                            'users.id',
                            'firstname',
                            'lastname',
                        ])
                        ->join('group_user', 'users.id', '=', 'group_user.user_id')
                        ->join('organization_role_users', 'organization_role_users.user_id', '=', 'group_user.user_id')
                        ->where('group_user.group_id', '=', $group)
                        ->where('organization_role_users.organization_id', '=', auth()->user()->current_organization_id)
                        ->where('organization_role_users.role_id', '=', 6)
                        ->get(),
                    'groups' => auth()->user()->currentGroupEnrolments()->where('curriculum_id', $enablingObjective->curriculum_id)->get()
        ];



        if (request()->wantsJson())
        {

            return $result;
        }
    }


}

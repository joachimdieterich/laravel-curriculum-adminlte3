<?php

namespace App\Http\Controllers;

use App\Config;
use App\Curriculum;
use App\Http\Requests\StoreTerminalObjectiveRequest;
use App\Http\Requests\UpdateTerminalObjectiveRequest;
use App\QuoteSubscription;
use App\ReferenceSubscription;
use App\TerminalObjective;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TerminalObjectiveController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTerminalObjectiveRequest $request)
    {
        abort_unless(Curriculum::find(request('curriculum_id'))->isAccessible(), 403);

        $order_id = $this->getMaxOrderId(request('curriculum_id'), request('objective_type_id'));
        $terminalObjective = TerminalObjective::create(array_merge($request->all(), ['order_id' => $order_id]));

        LogController::set(get_class($this).'@'.__FUNCTION__);

        if (request()->wantsJson()) {
            return ['message' => $terminalObjective];
            //return ['message' => $terminalObjective->path()];
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\TerminalObjective  $terminalObjective
     * @return \Illuminate\Http\Response
     */
    public function show(TerminalObjective $terminalObjective)
    {
        abort_unless($terminalObjective->isAccessible(), 403);

        $objective = TerminalObjective::where('id', $terminalObjective->id)
            ->with(['curriculum', 'curriculum.subject', 'variants',
                'enablingObjectives',
                'referenceSubscriptions.siblings.referenceable', 'quoteSubscriptions.siblings.quotable', ])
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
     * @param  \App\TerminalObjective  $terminalObjective
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTerminalObjectiveRequest $request, TerminalObjective $terminalObjective)
    {
        abort_unless($terminalObjective->isAccessible(), 403);

        //first get existing data to later adjust order_id
        $old_objective = TerminalObjective::find(request('id'));

        // update objective type
        if ($request->has('objective_type_id')) {
            $order_id = $this->getMaxOrderId(request('curriculum_id'), request('objective_type_id'));
            $request->request->add(['order_id' => $order_id]);
            // if moved to another curriculum
            if ($old_objective->curriculum_id != request('curriculum_id')) {
                if ($terminalObjective->update(['curriculum_id' => request('curriculum_id'), 'order_id' => $order_id]) == true) {
                    $this->moveToCurriculum($old_objective, $request);
                }
            } elseif (($terminalObjective->update($request->all()) == true) and ($old_objective->order_id != request('order_id'))) {
                $this->resetOrderIds($old_objective->curriculum_id, $old_objective->objective_type_id, $old_objective->order_id);
            }
            if (request()->wantsJson()) {
                return ['message' => $terminalObjective];
            }
        }

        // update order_id
        if ($request->has('order_id')) {
            if (request()->wantsJson()) {
                return ['message' => $this->toggleOrderId($old_objective, request('order_id'))];
            }
        }

        // default

        return $terminalObjective->update($request->all());
    }

    /**
     * do calculations when objective is moved to another curriculum
     *
     * @param  \App\TerminalObjective  $old_objective
     * @param $request
     */
    public function moveToCurriculum($old_objective, $request)
    {
        abort_unless(Curriculum::find($old_objective->curriculum_id)->isAccessible(), 403);

        $this->resetOrderIds($old_objective->curriculum_id, $old_objective->objective_type_id, $old_objective->order_id);
        DB::table('enabling_objectives')
            ->where('terminal_objective_id', $old_objective->id)
            ->update(['curriculum_id' => request('curriculum_id')]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\TerminalObjective  $terminalObjective
     * @return \Illuminate\Http\Response
     */
    public function destroy(TerminalObjective $terminalObjective)
    {
        abort_unless((\Gate::allows('objective_delete') and $terminalObjective->isAccessible()), 403);

        //set temp vars
        $curriculum_id = $terminalObjective->curiculum_id;
        $objective_type_id = $terminalObjective->objective_type_id;
        $order_id = $terminalObjective->order_id;

        $terminalObjective->enablingObjectives()->delete();

        // delete contents
        foreach ($terminalObjective->contents as $content) {
            (new ContentController)->destroy($content, 'App\TerminalObjective', $terminalObjective->id); // delete or unsubscribe if content is still subscribed elsewhere
        }

        //delete all achievements
        $terminalObjective->achievements()
                ->where('referenceable_type', '=', 'App\TerminalObjective')
                ->where('referenceable_id', '=', $terminalObjective->id)
                ->delete();

        // delete subscriptions
        $terminalObjective->subscriptions()
                ->where('terminal_objective_id', '=', $terminalObjective->id)
                ->delete();

        // delete mediaSubscriptions -> media will not be deleted
        $terminalObjective->mediaSubscriptions()
                ->where('subscribable_type', '=', 'App\TerminalObjective')
                ->where('subscribable_id', '=', $terminalObjective->id)
                ->delete();

        // delete progresses
        $terminalObjective->progresses()
                ->where('referenceable_type', '=', 'App\TerminalObjective')
                ->where('referenceable_id', '=', $terminalObjective->id)
                ->delete();

        // delete quoteSubscriptions
        $terminalObjective->quoteSubscriptions()
                ->where('quotable_type', '=', 'App\TerminalObjective')
                ->where('quotable_id', '=', $terminalObjective->id)
                ->delete();

        // delete referenceSubscriptions
        $terminalObjective->referenceSubscriptions()
                ->where('referenceable_type', '=', 'App\TerminalObjective')
                ->where('referenceable_id', '=', $terminalObjective->id)
                ->delete();

        // delete repositorySubscriptions
        $terminalObjective->repositorySubscriptions()
                ->where('subscribable_type', '=', 'App\TerminalObjective')
                ->where('subscribable_id', '=', $terminalObjective->id)
                ->delete();
        // delete prerequisites entries (predecessors)
        $terminalObjective->predecessors()
            ->where('successor_type', '=', 'App\TerminalObjective')
            ->where('successor_id', '=', $terminalObjective->id)
            ->delete();
        // delete prerequisites entries (successors)
        $terminalObjective->successors()
            ->where('predecessor_type', '=', 'App\TerminalObjective')
            ->where('predecessor_id', '=', $terminalObjective->id)
            ->delete();

        //delete objective
        $return = $terminalObjective->delete();

        //reset order_ids
        $this->resetOrderIds($curriculum_id, $objective_type_id, $order_id);

        if (request()->wantsJson()) {
            return ['message' => $return];
        }
        //return $return;
    }

    public function referenceSubscriptionSiblings(TerminalObjective $terminalObjective)
    {
        abort_unless($terminalObjective->isAccessible(), 403);

        $siblings = new Collection([]);

        foreach ($terminalObjective->referenceSubscriptions as $referenceSubscription) {
            $collection = ReferenceSubscription::where('reference_id', '=', $referenceSubscription->reference_id)
                ->where(function ($query) use ($referenceSubscription, $terminalObjective) {
                    return $query->where('reference_id', '=', $referenceSubscription->reference_id)
                                                     ->where('referenceable_id', '!=', $terminalObjective->id);
                })
                                ->with(['referenceable.curriculum.organizationType'])
                                ->with(['reference'])
                     ->get();
            $siblings = $siblings->merge($collection);
        }

        if (count($siblings) == 0) { //end early
            return ['message'=> 'no subscriptions'];
        }

        foreach ($siblings as $sibling) {
            $curricula_list[$sibling->referenceable->curriculum->id] = $sibling->referenceable->curriculum;
        }

        return ['siblings' => $siblings, 'curricula_list' => $curricula_list];
    }

    public function quoteSubscriptions(TerminalObjective $terminalObjective)
    {
        abort_unless($terminalObjective->isAccessible(), 403);

        $collection = QuoteSubscription::where('quotable_id', '=', $terminalObjective->id)
            ->where('quotable_type', '=', 'App\TerminalObjective')
            ->with(['quote.content.subscriptions.subscribable'])
            ->get();

        if (count($collection) == 0) { //end early
            return ['message' => 'no subscriptions'];
        }

        foreach ($collection as $quote_subscriptions) {
            $arr[$quote_subscriptions->quote_id] = ! is_null($quote_subscriptions->quote);

            if (! is_null($quote_subscriptions->quote)) {
                $curricula_list[$quote_subscriptions->quote->content->subscriptions[0]->subscribable->id] = $quote_subscriptions->quote->content->subscriptions[0]->subscribable;
                $quotes_subscriptions[] = $quote_subscriptions;
            }
        }

        return ['quotes_subscriptions' => $quotes_subscriptions, 'curricula_list' => $curricula_list];
    }

    protected function getMaxOrderId($curriculum_id, $objective_type_id)
    {
        abort_unless(Curriculum::find($curriculum_id)->isAccessible(), 403);

        $order_id = DB::table('terminal_objectives')
            ->where('curriculum_id', $curriculum_id)
            ->where('objective_type_id', $objective_type_id)
            ->max('order_id');

        return (is_numeric($order_id)) ? $order_id + 1 : 0;
    }

    protected function resetOrderIds($curriculum_id, $objective_type_id, $order_id, $direction = 'down')
    {
        abort_unless(Curriculum::find($curriculum_id)->isAccessible(), 403);

        return (new TerminalObjective)->where('curriculum_id', $curriculum_id)
            ->where('objective_type_id', $objective_type_id)
            ->where('order_id', '>', $order_id)
            ->update([
                'order_id' => DB::raw('order_id'.(($direction === 'down') ? '-1' : '+1')),
            ]);
    }

    /**
     * @param  int  $curriculum_id
     * @param  int  $objective_type_id
     * @param  int  $order_id
     * @param  int  $new_order_id
     * @return type
     */
    protected function toggleOrderId($old_objective, $new_order_id)
    {
        // toggle order_ids of terminal objectives
        $responseA = (new TerminalObjective)->where('curriculum_id', $old_objective->curriculum_id)
                            ->where('objective_type_id', $old_objective->objective_type_id)
                            ->where('order_id', '=', $new_order_id)
                            ->update(['order_id' => $old_objective->order_id]);

        $responseB = (new TerminalObjective)->where('id', $old_objective->id)
                                ->update(['order_id' => $new_order_id]);

        if (($responseA == true) and ($responseB == true)) {
            return '/curricula/'.$old_objective->curriculum_id;
        }
    }
}

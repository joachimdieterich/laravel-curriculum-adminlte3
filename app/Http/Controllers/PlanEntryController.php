<?php

namespace App\Http\Controllers;

use App\Medium;
use App\MediumSubscription;
use App\PlanEntry;
use App\TerminalObjective;
use Illuminate\Http\Request;

class PlanEntryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $input = $this->validateRequest();

        if (request()->wantsJson()) {
            return [
                'entries' => PlanEntry::where('plan_id', $input['plan_id'])->get(),
            ];
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        abort_unless(\Gate::allows('plan_create'), 403);
        $new_entry = $this->validateRequest();

        $entry = PlanEntry::create([
            'title' => $new_entry['title'],
            'description' => $new_entry['description'],
            'plan_id' => $new_entry['plan_id'],

            'css_icon' => $new_entry['css_icon'] ?? 'fas fa-calendar-day',
            'color' => $new_entry['color'] ?? '#2980B9',
            'order_id' => $new_entry['order_id'] ?? 0,

            'medium_id' => $new_entry['medium_id'] ?? null,

            'owner_id' => auth()->user()->id,
        ]);
        // subscribe embedded media
        checkForEmbeddedMedia($entry, 'description');
        if ($new_entry['medium_id'] != null) {
            $this->subscribeMedium($entry); // for medium_id
        }

        // axios call?
        if (request()->wantsJson()) {
            return ['entry' => $entry];
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\PlanEntry  $planEntry
     * @return \Illuminate\Http\Response
     */
    public function show(PlanEntry $planEntry)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\PlanEntry  $planEntry
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PlanEntry $planEntry)
    {
        abort_unless(\Gate::allows('plan_edit'), 403);
        $update_entry = $this->validateRequest();

        $medium_id = is_array($update_entry['medium_id']) ? $update_entry['medium_id'][0] : $update_entry['medium_id'];


        $planEntry->update([
            'title' => $update_entry['title'] ?? $planEntry->title,
            'description' => $update_entry['description'] ?? $planEntry->description,
            'plan_id' => $planEntry->plan_id,

            'css_icon' => $update_entry['css_icon'] ?? $planEntry->css_icon,
            'color' => $update_entry['color'] ?? $planEntry->color,
            'order_id' => $update_entry['order_id'] ?? $planEntry->order_id,

            'medium_id' => $medium_id ?? $planEntry->medium_id,

            'owner_id' => auth()->user()->id,
        ]);

        // subscribe embedded media
        checkForEmbeddedMedia($planEntry, 'description');
        if ($medium_id != null) {
            $this->subscribeMedium($planEntry); // for medium_id
        }
        // axios call?
        if (request()->wantsJson()) {
            return ['entry' => $planEntry];
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\PlanEntry  $planEntry
     * @return \Illuminate\Http\Response
     */
    public function destroy(PlanEntry $planEntry)
    {
        abort_unless(\Gate::allows('plan_delete'), 403);

        $planEntry->delete();

        if (request()->wantsJson()) {
            return [
                'deleted' => true,
            ];
        }
    }

    protected function subscribeMedium($entry){
        $subscribe = MediumSubscription::updateOrCreate([
            'medium_id' => $entry->medium_id,
            'subscribable_type' => 'App\PlanEntry',
            'subscribable_id' => $entry->id,
        ], [
            'sharing_level_id' => 1,
            'visibility' => 1,
            'owner_id' => auth()->user()->id,
        ]);
        $subscribe->save();
    }

    protected function validateRequest()
    {
        return request()->validate([
            'id' => 'sometimes|integer|nullable',
            'title' => 'sometimes|string',
            'description' => 'sometimes|nullable',
            'plan_id' => 'sometimes|integer',
            'css_icon' => 'sometimes|string',
            'order_id' => 'sometimes|integer',
            'color' => 'sometimes|string',
            'medium_id' => 'sometimes|nullable',
        ]);
    }
}

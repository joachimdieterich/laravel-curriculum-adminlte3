<?php

namespace App\Http\Controllers;

use App\Plan;
use App\PlanEntry;
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
        abort_unless(\Gate::allows('plan_create') and Plan::find($request->plan_id)->isEditable(), 403, "No permission to create entries for this plan");
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

        // axios call?
        if (request()->wantsJson()) {
            return $entry;
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
        abort_unless(\Gate::allows('plan_edit') and Plan::find($request->plan_id)->isEditable(), 403, "No permission to edit entries of this plan");
        $update_entry = $this->validateRequest();

        $medium_id = is_array($update_entry['medium_id']) ? $update_entry['medium_id'][0] : $update_entry['medium_id'];

        $planEntry->update([
            'title' => $update_entry['title'] ?? $planEntry->title,
            'description' => $update_entry['description'],
            'plan_id' => $planEntry->plan_id,
            'css_icon' => $update_entry['css_icon'] ?? $planEntry->css_icon,
            'color' => $update_entry['color'] ?? $planEntry->color,
            'order_id' => $update_entry['order_id'] ?? $planEntry->order_id,
            'medium_id' => $medium_id,
        ]);

        // axios call?
        if (request()->wantsJson()) {
            return $planEntry;
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
        $user_id = auth()->user()->id;
        abort_unless(\Gate::allows('plan_delete') and (
            is_admin()
            or $planEntry->owner_id == $user_id
            or Plan::find($planEntry->plan_id)->owner_id == $user_id
        ), 403, "No permission to delete entry, only plan-owner or entry-owner");

        $planEntry->delete();

        if (request()->wantsJson()) {
            return true;
        }
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

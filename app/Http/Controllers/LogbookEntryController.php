<?php

namespace App\Http\Controllers;

use App\Logbook;
use App\LogbookEntry;
use Illuminate\Http\Request;

class LogbookEntryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     */
    public function index()
    {
        abort(403);
    }

    /**
     * Show the form for creating a new resource.
     *
     */
    public function create()
    {
        abort(403);
    }

    /**
     * Update the subject_id of a specific entry
     */
    public function setSubject(LogbookEntry $logbookEntry) {
        $input = $this->validateRequest();

        $logbookEntry->update([
            'subject_id' => format_select_input($input['subject_id']),
        ]);
        if (request()->wantsJson()) {
            return $logbookEntry->subject;
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
        $new_entry = $this->validateRequest();
        $model = Logbook::find($new_entry['logbook_id']);
        abort_unless((\Gate::allows('logbook_entry_create') and $model->isAccessible()), 403);

        $entry = LogbookEntry::firstOrCreate([
            'logbook_id' => $new_entry['logbook_id'],
            'title' => $new_entry['title'],
            'description' => $new_entry['description'],
            'begin' => $new_entry['begin'],
            'end' => $new_entry['end'],
            'subject_id' => null,
            'owner_id' => auth()->user()->id,
        ]);

        LogController::set(get_class($this).'@'.__FUNCTION__);

        $entry = $entry->with([
            'owner' => function ($query) {
                $query->select('id', 'username', 'firstname', 'lastname', 'medium_id');
            },
            'absences.owner' => function ($query) {
                $query->select('id', 'username', 'firstname', 'lastname', 'medium_id');
            }, //todo: lazyload
            'absences.absent_user',
            'terminalObjectiveSubscriptions.terminalObjective',
            'enablingObjectiveSubscriptions.enablingObjective.terminalObjective',
            'taskSubscription.task.subscriptions' => function ($query) {
                $query->where('subscribable_id', auth()->user()->id)
                    ->where('subscribable_type', 'App\User');
            },
        ])->where('id', $entry->id)->get()->first();

        if (request()->wantsJson()) {
            return $entry;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\LogbookEntry  $logbookEntry
     * @return \Illuminate\Http\Response
     */
    public function show(LogbookEntry $logbookEntry)
    {
        abort_unless((\Gate::allows('logbook_show') and $logbookEntry->isAccessible()), 403);

        if (request()->wantsJson()) {
            return [
                'message' => $logbookEntry,
            ];
        } else {
            return redirect()->action('LogbookController@show', ['logbook' => $logbookEntry->logbook_id]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\LogbookEntry  $logbookEntry
     * @return \Illuminate\Http\Response
     */
    public function update(LogbookEntry $logbookEntry)
    {
        abort_unless((\Gate::allows('logbook_edit') and $logbookEntry->isAccessible()), 403);

        $logbookEntry->update($this->validateRequest());

        if (request()->wantsJson()) {
            return $logbookEntry;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\LogbookEntry  $logbookEntry
     * @return \Illuminate\Http\Response
     */
    public function destroy(LogbookEntry $logbookEntry)
    {
        //delete all relations
        abort_unless((\Gate::allows('logbook_delete') and $logbookEntry->isAccessible()), 403);

        // delete mediaSubscriptions -> media will not be deleted
        $logbookEntry->mediaSubscriptions()
            ->where('subscribable_type', '=', 'App\LogbookEntry')
            ->where('subscribable_id', '=', $logbookEntry->id)
            ->delete();

        // delete terminalObjectiveSubscriptions
        $logbookEntry->terminalObjectiveSubscriptions()
            ->where('subscribable_type', '=', 'App\LogbookEntry')
            ->where('subscribable_id', '=', $logbookEntry->id)
            ->delete();

        // delete enablingObjectiveSubscriptions
        $logbookEntry->enablingObjectiveSubscriptions()
            ->where('subscribable_type', '=', 'App\LogbookEntry')
            ->where('subscribable_id', '=', $logbookEntry->id)
            ->delete();

        // delete contents
        foreach ($logbookEntry->contents as $content) {
            (new ContentController)->destroy($content, 'App\LogbookEntry', $logbookEntry->id); // delete or unsubscribe if content is still subscribed elsewhere
        }

        // delete taskSubscription
        $logbookEntry->taskSubscription()
            ->where('subscribable_type', '=', 'App\LogbookEntry')
            ->where('subscribable_id', '=', $logbookEntry->id)
            ->delete();

        // delete absences
        $logbookEntry->absences()
            ->where('referenceable_type', '=', 'App\LogbookEntry')
            ->where('referenceable_id', '=', $logbookEntry->id)
            ->delete();

        if (request()->wantsJson()) {
            return $logbookEntry->delete();
        }
    }

    protected function validateRequest()
    {
        return request()->validate([
            'logbook_id'        => 'sometimes|required',
            'title'             => 'sometimes|required',
            'description'       => 'sometimes',
            'begin'             => 'sometimes',
            'end'               => 'sometimes',
            'owner_id'          => 'sometimes',
            'subject_id'        => 'sometimes',
        ]);
    }
}

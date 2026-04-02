<?php

namespace App\Http\Controllers;

use App\Logbook;
use Illuminate\Http\Request;

class LogbookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort_unless(\Gate::allows('logbook_access'), 403);

        if (request()->wantsJson())
        {
            return getEntriesForSelect2ByCollection(
                getSubscribedModels(Logbook::class),
                'logbooks.'
            );
        }

        return view('logbooks.index');
    }

    public function list(Request $request)
    {
        abort_unless(\Gate::allows('logbook_access'), 403);

        $logbooks = Logbook::select('logbooks.id', 'title', 'description', 'medium_id', 'color', 'css_icon', 'owner_id');
        $withSubscribed = false;
        $withOwned = false;

        if (request()->has(['group_id']))
        {
            $group_id = request()->validate(
                [
                    'group_id' => 'required',
                ]
            )['group_id'];

            $logbooks = Logbook::whereHas('subscriptions', function ($query) use ($group_id) {
                $query->where(function ($query) use ($group_id) {
                    $query->where('subscribable_type', 'App\\Group')
                        ->where('subscribable_id', $group_id);
                });
            });
        }
        else
        {
            switch ($request->filter) {
                case 'owner':           $withOwned = true;
                    break;
                case 'shared_with_me':  $withSubscribed = true;
                    break;
                case 'shared_by_me':    $logbooks->where('owner_id', auth()->user()->id)->whereHas('subscriptions');
                    break;
                case 'all':
                default:                $withSubscribed = $withOwned = true;
                    break;
            }
        }

        return getDataTableWithEntries($logbooks, $withSubscribed, $withOwned);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort(405);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        abort_unless(\Gate::allows('logbook_create'), 403);
        abort_unless(limiter(
            'App\\Role',
            auth()->user()->role()->id,
            'logbook_limiter',
            'App\\Logbook',
            'owner_id'), 402); //is there an role limit?

        $new_logbook = $this->validateRequest();

        $logbook = Logbook::Create([
            'title' => $new_logbook['title'],
            'description' => $new_logbook['description'],
            'medium_id' => $new_logbook['medium_id'] ?? null,
            'color' => $new_logbook['color'] ?? '#2980B9',
            'css_icon' => $new_logbook['css_icon'],
            'owner_id' => auth()->user()->id,
        ]);

        //subscribe to model
        if (isset($new_logbook['subscribable_type']) and isset($new_logbook['subscribable_id'])) {
            $model = $new_logbook['subscribable_type']::find($new_logbook['subscribable_id']);
            $logbook->subscribe($model);
        }

        LogController::set(get_class($this).'@'.__FUNCTION__);

        if (request()->wantsJson()) {
            return $logbook;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Logbook  $logbook
     * @return \Illuminate\Http\Response
     */
    public function show(Logbook $logbook)
    {
        $this->checkPermissions($logbook, 'access');

        $logbook = $logbook->with([
            'owner' => function ($query) {
                $query->select('id', 'username', 'firstname', 'lastname', 'medium_id');
            },
            'subscriptions.subscribable',
            'entries.owner' => function ($query) {
                $query->select('id', 'username', 'firstname', 'lastname', 'medium_id');
            },
            'entries.absences.owner' => function ($query) {
                $query->select('id', 'username', 'firstname', 'lastname', 'medium_id');
            },
            'entries.subject' => function ($query) {
                $query->select('id', 'title');
            },
            'entries.absences.absent_user',
            'entries.terminalObjectiveSubscriptions.terminalObjective',
            'entries.enablingObjectiveSubscriptions.enablingObjective.terminalObjective',
            'entries.taskSubscription.task.subscriptions' => function ($query) {
                $query->where('subscribable_id', auth()->user()->id)
                    ->where('subscribable_type', 'App\User');
            },
        ])->find($logbook->id);

        return view('logbooks.show')
            ->with(compact('logbook'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Logbook  $logbook
     * @return \Illuminate\Http\Response
     */
    public function edit(Logbook $logbook)
    {
        abort(405);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Logbook  $logbook
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Logbook $logbook)
    {
        $this->checkPermissions($logbook, 'edit');
        $input = $this->validateRequest();
        $logbook->update([
            'title' => $input['title'],
            'description' => $input['description'],
            'medium_id' => $input['medium_id'],
            'color' => $input['color'],
            'css_icon' => $input['css_icon'],
            'owner_id' => is_admin() ? $input['owner_id'] : $logbook->owner_id,
        ]);

        return $logbook;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Logbook  $logbook
     * @return \Illuminate\Http\Response
     */
    public function destroy(Logbook $logbook)
    {
        $this->checkPermissions($logbook, 'delete');

        foreach ($logbook->entries as $entries) {
            (new LogbookEntryController())->destroy($entries);
        }
        foreach ($logbook->subscriptions as $subscription) {
            (new LogbookSubscriptionController())->destroy($subscription);
        }

        if ($logbook->delete()){
            return view('logbooks.index');
        }


    }

    /**
     * Print the specified resource.
     *
     * @param  \App\Logbook  $logbook
     * @return \Illuminate\Http\Response
     */
    public function print(Logbook $logbook)
    {
        $this->checkPermissions($logbook, 'access');

        $input = request()->validate([
            'begin' => 'required',
            'end' => 'required',
            'showDescription' => 'string',
            'showContents' => 'string',
            'showTasks' => 'string',
            'showMedia' => 'string',
            'showReferences' => 'string',
            'showAbsences' => 'string',
        ]);

        $html = view('print.logbook')
            ->with(compact('logbook'))
            ->with(compact('input'))
            ->render();

        return (new PrintController)->print($html, $logbook->title.'.pdf', 'download', 'portrait');
    }

    /**
     * @param  Logbook  $logbook
     */
    private function checkPermissions(Logbook $logbook, $action = 'create'): void
    {
        abort_unless(\Gate::allows('logbook_'.$action) and $logbook->isAccessible(), 403);

        /*
         * Check for role limiter
         */
        if ($action === 'create') {
            abort_unless(limiter(
                'App\\Role',
                auth()->user()->role()->id,
                'logbook_limiter',
                'App\\Logbook',
                'owner_id'
            ), 402);
        }

        if ($action === 'edit') {
            abort_unless(auth()->user()->id == $logbook->owner_id || is_admin(), 403); // owner or admin
            //todo: if user has subscription editable == true
        }

        if ($action === 'delete') {
            abort_unless(auth()->user()->id == $logbook->owner_id || is_admin(), 403); // owner or admin
        }
    }

    protected function validateRequest()
    {
        return request()->validate([
            'title' => 'sometimes|required',
            'description' => 'sometimes',
            'medium_id' => 'sometimes',
            'color' => 'sometimes',
            'css_icon' => 'sometimes',
            'subscribable_type' => 'sometimes',
            'subscribable_id' => 'sometimes',
            'owner_id' => 'sometimes',
        ]);
    }

}

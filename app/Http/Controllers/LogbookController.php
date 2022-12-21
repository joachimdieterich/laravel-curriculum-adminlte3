<?php

namespace App\Http\Controllers;

use App\Logbook;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class LogbookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_unless(\Gate::allows('logbook_access'), 403);

        return view('logbooks.index');
    }

    public function list()
    {
        abort_unless(\Gate::allows('logbook_access'), 403);

        $logbooks = is_admin() ? Logbook::with('subscriptions') : Logbook::with('subscriptions')
            ->whereHas('subscriptions', function ($query) {
                $query->where(
                    function ($query) {
                        $query->where('subscribable_type', 'App\\Course')->whereIn('subscribable_id', auth()->user()->currentGroupEnrolments->pluck('course_id'));
                    })
                    ->orWhere(
                        function ($query) {
                            $query->where('subscribable_type', 'App\\Organization')->where('subscribable_id', auth()->user()->current_organization_id);
                        })
                    ->orWhere(
                        function ($query) {
                            $query->where('subscribable_type', 'App\\Group')->whereIn('subscribable_id', auth()->user()->groups->pluck('id'));
                        })
                    ->orWhere(
                        function ($query) {
                            $query->where('subscribable_type', 'App\\User')->where('subscribable_id', auth()->user()->id);
                        });
            })
            ->orWhere('owner_id', auth()->user()->id);

        $edit_gate = \Gate::allows('logbook_edit');
        $delete_gate = \Gate::allows('logbook_delete');

        return empty($logbooks) ? '' : DataTables::of($logbooks)
            ->addColumn('action', function ($logbooks) use ($edit_gate, $delete_gate) {
                $actions = '';
                if ($edit_gate and $logbooks->owner_id == auth()->user()->id) {
                    $actions .= '<a href="'.route('logbooks.edit', $logbooks->id).'" '
                        .'id="edit-logbook-'.$logbooks->id.'" '
                        .'class="px-2 text-black">'
                        .'<i class="fa fa-pencil-alt"></i>'
                        .'</a>';
                }
                if ($delete_gate and $logbooks->owner_id == auth()->user()->id) {
                    $actions .= '<button type="button" class="btn text-danger" onclick="event.preventDefault();destroyDataTableEntry(\'logbooks\','.$logbooks->id.');"><i class="fa fa-trash"></i></button>';
                }

                return $actions;
            })
            ->addColumn('check', '')
            ->setRowId('id')
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_unless(\Gate::allows('logbook_create'), 403);
        abort_unless(limiter(
            'App\\Role',
            auth()->user()->role()->id,
            'logbook_limiter',
            'App\\Logbook',
            'owner_id'), 402); //is there an role limit?

        $logbooks = Logbook::all();

        return view('logbooks.create')
            ->with(compact('logbooks'));
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
            'owner_id' => auth()->user()->id,
        ]);

        //subscribe to model
        if (isset($new_logbook['subscribable_type']) and isset($new_logbook['subscribable_id'])) {
            $model = $new_logbook['subscribable_type']::find($new_logbook['subscribable_id']);
            $logbook->subscribe($model);
        }

        LogController::set(get_class($this).'@'.__FUNCTION__);

        // axios call?
        if (request()->wantsJson()) {
            return ['message' => $logbook->path()];
        }

        return redirect($logbook->path());
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
            }, //todo: lazyload
            'entries.absences.owner' => function ($query) {
                $query->select('id', 'username', 'firstname', 'lastname', 'medium_id');
            }, //todo: lazyload
            'entries.absences.absent_user',
            'entries.terminalObjectiveSubscriptions.terminalObjective',
            'entries.enablingObjectiveSubscriptions.enablingObjective.terminalObjective',
            'entries.taskSubscription.task.subscriptions' => function ($query) {
                $query->where('subscribable_id', auth()->user()->id)
                    ->where('subscribable_type', 'App\User');
            },
        ])->where('id', $logbook->id)->get()->first();

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
        $this->checkPermissions($logbook, 'edit');

        return view('logbooks.edit')
            ->with(compact('logbook'));
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

        $logbook->update([
            'title' => $request['title'],
            'description' => $request['description'],
        ]);

        return redirect()->route('logbooks.index');
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
        $logbook->delete();

        return back();
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
        abort_unless((\Gate::allows('logbook_'.$action) and $logbook->isAccessible()), 403);

        /*
         * Check for role limiter
         */
        if ($action === 'create') {
            abort_unless(limiter(
                'App\\Role',
                auth()->user()->role()->id,
                'logbook_limiter',
                'App\\Logbook',
                'owner_id'), 402);
        }

        if ($action === 'edit') {
            abort_unless(auth()->user()->id == $logbook->owner_id, 403);                // user owns logbook
            //todo: if user has subscription editable == true
        }

        if ($action === 'delete') {
            abort_unless(auth()->user()->id == $logbook->owner_id, 403);                // user owns logbook
        }
    }

    protected function validateRequest()
    {
        return request()->validate([
            'title' => 'sometimes|required',
            'description' => 'sometimes',
            'subscribable_type' => 'sometimes',
            'subscribable_id' => 'sometimes',
        ]);
    }
}

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
        return view('logbooks.index');
    }

    public function list()
    {

        abort_unless(\Gate::allows('logbook_access'), 403);

        $logbooks = (auth()->user()->role()->id == 1) ? Logbook::all() : Logbook::with('subscriptions')
            ->whereHas('subscriptions', function($query)  {
                 $query->where('subscribable_type', "App\Group")->whereIn('subscribable_id', auth()->user()->groups->pluck('id'))
                    ->orWhere('subscribable_type', "App\Course")->whereIn('subscribable_id', auth()->user()->currentGroupEnrolments->pluck('course_id'));
            })->get();

        $edit_gate = \Gate::allows('logbook_edit');
        $delete_gate = \Gate::allows('logbook_delete');

        return empty($logbooks) ? '' : DataTables::of($logbooks)
            ->addColumn('action', function ($logbooks) use ($edit_gate, $delete_gate) {
                 $actions  = '';
                    if ($edit_gate){
                        $actions .= '<a href="'.route('logbooks.edit', $logbooks->id).'" '
                                    . 'id="edit-logbook-'.$logbooks->id.'" '
                                    . 'class="px-2 text-black">'
                                    . '<i class="fa fa-pencil-alt"></i>'
                                    . '</a>';
                    }
                    if ($delete_gate){
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
        $new_logbook = $this->validateRequest();

        $logbook = Logbook::Create([
            'title'         => $new_logbook['title'],
            'description'   => $new_logbook['description'],
            'owner_id'      => auth()->user()->id,
        ]);

        //subscribe to model
        if (isset($new_logbook['subscribable_type']) AND isset($new_logbook['subscribable_id'])){
            $model = $new_logbook['subscribable_type']::find($new_logbook['subscribable_id']);
            $logbook->subscribe($model);
        }

        // axios call?
        if (request()->wantsJson()){
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
        //todo: check if user has permission to see logbook
        abort_unless((auth()->user()->logbooks->contains('id', $logbook->id) // user owns logbook
            OR ($logbook->subscriptions->where('subscribable_type', "App\Group")->whereIn('subscribable_id', auth()->user()->groups->pluck('id')))->isNotEmpty() //user is enroled in group
            OR ($logbook->subscriptions->where('subscribable_type', "App\Course")->whereIn('subscribable_id', auth()->user()->currentGroupEnrolments->pluck('course_id')))->isNotEmpty()
            OR (auth()->user()->currentRole()->first()->id == 1)), 403);                // or admin
        $logbook = $logbook->with([
                'subscriptions.subscribable',
                'entries.absences.owner', //todo: lazyload
                'entries.absences.absent_user',
                'entries.terminalObjectiveSubscriptions.terminalObjective',
                'entries.enablingObjectiveSubscriptions.enablingObjective.terminalObjective',
                'entries.taskSubscription.task.subscriptions' => function($query) {
                     $query->where('subscribable_id', auth()->user()->id)
                           ->where('subscribable_type', 'App\User');
                 }
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
        abort_unless(\Gate::allows('logbook_edit'), 403);

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
        abort_unless(\Gate::allows('logbook_edit'), 403);

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
        abort_unless(\Gate::allows('logbook_delete'), 403);

        $logbook->delete();

        //return back();
    }

    protected function validateRequest()
    {

        return request()->validate([
            'title'         => 'sometimes|required',
            'description'   => 'sometimes',
            'subscribable_type' => 'sometimes',
            'subscribable_id'   => 'sometimes',
        ]);
    }
}

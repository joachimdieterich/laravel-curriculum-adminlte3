<?php

namespace App\Http\Controllers;

use App\Meeting;
use App\Plugins\Eventmanagement\EventmanagementPlugin;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class MeetingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //abort_unless(\Gate::allows('meeting_access'), 403);

        return view('meetings.index');
    }
    public function list()
    {
        //abort_unless(\Gate::allows('meeting_access'), 403);
        $meetings = Meeting::select([
            'id',
            'uid',
            'title',
            'begin',
            'end',
        ])->get();

        $edit_gate = \Gate::allows('meeting_edit');
        $delete_gate = \Gate::allows('meeting_delete');

        return DataTables::of($meetings)
            ->addColumn('action', function ($meetings) use ($edit_gate, $delete_gate) {
                $actions = '';
                if ($edit_gate) {
                    $actions .= '<a href="'.route('meetings.edit', $meetings->id).'" '
                        .'id="edit-meeting-'.$meetings->id.'" '
                        .'class="btn">'
                        .'<i class="fa fa-pencil-alt"></i>'
                        .'</a>';
                }
                if ($delete_gate) {
                    $actions .= '<button type="button" '
                        .'class="btn text-danger" '
                        .'onclick="destroyDataTableEntry(\'meetings\','.$meetings->id.')">'
                        .'<i class="fa fa-trash"></i></button>';
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
        return view('meetings.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //abort_unless(\Gate::allows('logbook_create'), 403);

        $new_meeting = $this->validateRequest();
        $vm = new EventmanagementPlugin();
        $events = $vm->plugins[env('EVENTMANAGEMENTPLUGIN')]->lesePlrlpVeranstaltungen(['search'=> $new_meeting['uid']]);

        $event = $events->lesePlrlpVeranstaltungen->data->key_0;
        $meeting = Meeting::Create([
            'uid' => $event->ARTIKEL_NR ?: $new_meeting['uid'],
            'accesstoken' => '',
            'title' => $event->ARTIKEL,
            'subtitle' => '',
            'description' => $event->BEMERKUNG,
            'begin' => $event->B_DAT,
            'end' => $event->E_DAT,
            'status' => $event->PLAN_STAT,
            'category' => $event->KATEGORIE,
            /*'target_group' => $event->ZIELGRUPPE,*/
            'url' => $event->LINK_DETAIL,
            'provider' => $event->ANBIETER,
            'medium_id' => $new_meeting['medium_id'],

            'owner_id' => auth()->user()->id,
        ]);
        return view('meetings.index');
      //  dump($events);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Meeting  $meeting
     * @return \Illuminate\Http\Response
     */
    public function show(Meeting $meeting)
    {
        return view('meetings.show')
            ->with(compact('meeting'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Meeting  $meeting
     * @return \Illuminate\Http\Response
     */
    public function edit(Meeting $meeting)
    {
        return view('meetings.edit')
            ->with(compact('meeting'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Meeting  $meeting
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Meeting $meeting)
    {
        $new_meeting = $this->validateRequest();

        $meeting->update([
            'uid' => $new_meeting['uid'],
            'description' => $new_meeting['description'],
            'info' => $new_meeting['info'],
            'speakers' => $new_meeting['speakers'],
        ]);

        return redirect($meeting->path());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Meeting  $meeting
     * @return \Illuminate\Http\Response
     */
    public function destroy(Meeting $meeting)
    {
        //
    }

    protected function validateRequest()
    {
        return request()->validate([
            'uid' => 'sometimes|required',
            'accesstoken' => 'sometimes',
            'title' => 'sometimes|required',
            'subtitle' => 'sometimes|required',
            'description' => 'sometimes',
            'info' => 'sometimes',
            'speakers' => 'sometimes',
            'begin' => 'sometimes',
            'end' => 'sometimes',
            'status' => 'sometimes',
            'category' => 'sometimes',
            'target_group' => 'sometimes',
            'url' => 'sometimes',
            'provider' => 'sometimes',
            'medium_id' => 'sometimes',
            'owner_id' => 'sometimes',
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Meeting;
use App\MeetingDate;
use App\Plugins\Eventmanagement\EventmanagementPlugin;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;

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
                        .'onclick="event.preventDefault();destroyDataTableEntry(\'meetings\','.$meetings->id.');">'
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
        //dump($events);
        $event = $events->lesePlrlpVeranstaltungen->data->key_0;
        // dump($event);
        $meeting = Meeting::Create([
            'uid' => $event->ARTIKEL_NR ?: $new_meeting['uid'],
            'access_token' => '',
            'title' => $event->ARTIKEL,
            'subtitle' => '',
            'description' => nl2br($event->BEMERKUNG),
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

        foreach ( (array) $event->termine as $index => $termin) {
         /*   dump($termin);
            dump($termin->DATUM.' '.$termin->BEGINN);*/
            MeetingDate::Create([
                'meeting_id' => $meeting->id,
                'uid' => $termin->ARTIKEL_NR ?: $new_meeting['uid'],
                'access_token' => '',
                'title' => Carbon::createFromFormat('Y-m-d H:i:s', $termin->DATUM.' '.$termin->BEGINN.':00')->format('d.m.y').' '. $termin->ARTIKEL,
                'address' => $termin->VO_ADRESSE,
                'begin' => Carbon::createFromFormat('Y-m-d H:i:s', $termin->DATUM.' '.$termin->BEGINN.':00'),
                'end' => Carbon::createFromFormat('Y-m-d H:i:s', $termin->DATUM.' '.$termin->ENDE.':00'),
                'type' => $termin->VO_ORT,

                'owner_id' => auth()->user()->id,
            ]);
        }
        return view('meetings.index');

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
            'livestream' => $new_meeting['livestream'],
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
        $meeting->dates()->delete();
        $return = $meeting->delete();
        if (request()->wantsJson()) {
            return ['message' => $return];
        }
    }

    protected function validateRequest()
    {
        return request()->validate([
            'uid' => 'sometimes|required',
            'access_token' => 'sometimes',
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
            'livestream' => 'sometimes',
        ]);
    }
}

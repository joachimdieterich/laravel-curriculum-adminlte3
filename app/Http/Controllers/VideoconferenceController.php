<?php

namespace App\Http\Controllers;

use App\Videoconference;
use App\VideoconferenceSubscription;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class VideoconferenceController extends Controller
{
    private $adapter;

    public function __construct(){
        $this->adapter = env('VIDEOCONFERENCE_ADAPTER');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_unless(\Gate::allows('videoconference_access'), 403);

        return view('videoconference.index');
    }

    public function list()
    {
        abort_unless(\Gate::allows('videoconference_access'), 403);
        $videoconferences = (auth()->user()->role()->id == 1) ? Videoconference::all() : auth()->user()->videoconferences()->get();

        $delete_gate = \Gate::allows('videoconference_delete');

        return DataTables::of($videoconferences)
            ->addColumn('action', function ($videoconferences) use ($delete_gate) {
                $actions = '';
                if ($delete_gate) {
                    $actions .= '<button type="button" '
                        .'class="btn text-danger" '
                        .'onclick="destroyDataTableEntry(\'videoconferences\','.$videoconferences->id.')">'
                        .'<i class="fa fa-trash"></i></button>';
                }

                return $actions;
            })

            ->addColumn('check', '')
            ->setRowId('id')
            ->setRowAttr([
                'color' => 'primary',
            ])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {


    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        abort_unless(\Gate::allows('videoconference_create'), 403);

        $input = $this->validateRequest();

        $conference =  (new $this->adapter())->create([
            'meetingID'     => $input['meetingID'],
            'meetingName'   => $input['meetingName'],
            'attendeePW'    => $input['attendeePW'],
            'moderatorPW'   => $input['moderatorPW'],
            'endCallbackUrl'=> $input['endCallbackUrl'],
            'logoutUrl'     => $input['logoutUrl'],

        ]);

        $videoconference = Videoconference::updateOrCreate([
            'meetingID'     => $conference['meetingID'],
            'meetingName'   => $input['meetingName'],
            'attendeePW'    => $conference['attendeePW'],
            'moderatorPW'   => $conference['moderatorPW'],
            'callbackURL'   => $input['endCallbackUrl'],
            'owner_id'      => auth()->user()->id,
        ]);

        if (isset($input['subscribable_type']) AND isset($input['subscribable_id']))
        {
            $subscribe = VideoconferenceSubscription::create([
                'videoconference_id' => $videoconference->id,
                'subscribable_type' => $input['subscribable_type'],
                'subscribable_id' => $input['subscribable_id'],
                'editable' => $input['editable'] ?? false,
                'owner_id' => auth()->user()->id,
            ]);
            $subscribe->save();
        }


        if (request()->wantsJson()) {
            return ['videoconference' => $videoconference];
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Videoconference  $videoconference
     * @return \Illuminate\Http\Response
     */
    public function show(Videoconference $videoconference)
    {
        abort_unless(\Gate::allows('videoconference_show'), 403);

        return (new $this->adapter())->start([
            'meetingID'     => $videoconference->meetingID,
            'meetingName'   => $videoconference->meetingName,
            'attendeePW'    => $videoconference->attendeePW,
            'moderatorPW'   => $videoconference->moderatorPW,
            'userName'      => auth()->user()->fullName()
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Videoconference  $videoconference
     * @return \Illuminate\Http\Response
     */
    public function edit(Videoconference $videoconference)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Videoconference  $videoconference
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Videoconference $videoconference)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Videoconference  $videoconference
     * @return \Illuminate\Http\Response
     */
    public function destroy(Videoconference $videoconference)
    {
        abort_unless(\Gate::allows('videoconference_delete'), 403);

        $videoconference->subscriptions()->delete();

        if (request()->wantsJson()) {
            return ['message' => $videoconference->delete()];
        }

        return $videoconference->delete();
    }

    protected function validateRequest()
    {
        return request()->validate([
            'meetingID' => 'sometimes',
            'meetingName' => 'sometimes',
            'attendeePW' => 'sometimes',
            'moderatorPW' => 'sometimes',
            'presentation' => 'sometimes',
            'recordID' => 'sometimes',
            'state' => 'sometimes',
            'userName' => 'sometimes',
            'password' => 'sometimes',
            'endCallbackUrl' => 'sometimes',
            'logoutUrl' => 'sometimes',
            'getRaw' => 'sometimes',
            'hooksID' => 'sometimes',
            'subscribable_type' => 'sometimes|string',
            'subscribable_id'   => 'sometimes|integer',
        ]);
    }

}

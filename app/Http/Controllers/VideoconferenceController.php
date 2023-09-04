<?php

namespace App\Http\Controllers;

use App\Kanban;
use App\Videoconference;
use App\VideoconferenceSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
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
        $edit_gate = \Gate::allows('videoconference_edit');

        return DataTables::of($videoconferences)
            ->addColumn('action', function ($videoconferences) use ($edit_gate, $delete_gate) {

                $actions = '<a href="'.route('videoconferences.show', $videoconferences->id).'" '
                    .'id="show-videoconference-'.$videoconferences->id.'" '
                    .'class="btn p-1">'
                    .'<i class="fa fa-list-alt"></i>'
                    .'</a>';
                if ($edit_gate) {
                    $actions .= '<a href="'.route('videoconferences.edit', $videoconferences->id).'" '
                        .'id="edit-videoconference-'.$videoconferences->id.'" '
                        .'class="btn">'
                        .'<i class="fa fa-pencil-alt"></i>'
                        .'</a>';
                }
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
        abort_unless(\Gate::allows('videoconference_create'), 403);

        return view('videoconference.create');

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

        $callbackUrl = $input['endCallbackUrl'] ?? $input['logoutUrl'];
        if ($callbackUrl == null)
        {
            $callbackUrl == env('APP_URL');
        }

        $conference =  (new $this->adapter())->create([
            'meetingID'     => $input['meetingID'] ?? Str::uuid(),
            'meetingName'   => $input['meetingName'],
            'attendeePW'    => $input['attendeePW'],
            'moderatorPW'   => $input['moderatorPW'],
            'endCallbackUrl'=> $callbackUrl,
            'logoutUrl'     => $input['logoutUrl'],

        ]);

        $videoconference = Videoconference::updateOrCreate([
            'meetingID'     => $conference['meetingID'],
            'meetingName'   => $input['meetingName'],
            'attendeePW'    => $conference['attendeePW'],
            'moderatorPW'   => $conference['moderatorPW'],
            'callbackUrl'   => $callbackUrl,
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

        return view('videoconference.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Videoconference  $videoconference
     * @return \Illuminate\Http\Response
     */
    public function show(Videoconference $videoconference)
    {
        abort_unless(\Gate::allows('videoconference_show') AND $videoconference->isAccessible(), 403);

        if (auth()->user()->role()->id == 1)
        {
            return (new $this->adapter())->start([
                'meetingID'                             => $videoconference->meetingID,
                'meetingName'                           => $videoconference->meetingName,
                'attendeePW'                            => $videoconference->attendeePW,
                'moderatorPW'                           => $videoconference->moderatorPW,
                'presentation'                          => $this->getPresentations($videoconference),
                'userName'                              => auth()->user()->fullName(),
                'callbackUrl'                           => $videoconference->callbackUrl,
                'welcomeMessage'                        => $videoconference->welcomeMessage,
                'dialNumber'                            => $videoconference->dialNumber,
                'maxParticipants'                       => $videoconference->maxParticipants,
                'logoutUrl'                             => $videoconference->logoutUrl,
                'record'                                => $videoconference->record,
                'duration'                              => $videoconference->duration,
                'isBreakout'                            => $videoconference->isBreakout,
                'moderatorOnlyMessage'                  => $videoconference->moderatorOnlyMessage,
                'autoStartRecording'                    => $videoconference->autoStartRecording,
                'allowStartStopRecording'               => $videoconference->allowStartStopRecording,
                'bannerText'                            => $videoconference->bannerText,
                'bannerColor'                           => $videoconference->bannerColor,
                'logo'                                  => $videoconference->logo,
                'copyright'                             => $videoconference->copyright,
                'muteOnStart'                           => $videoconference->muteOnStart,
                'allowModsToUnmuteUsers'                => $videoconference->allowModsToUnmuteUsers,
                'lockSettingsDisableCam'                => $videoconference->lockSettingsDisableCam,
                'lockSettingsDisableMic'                => $videoconference->lockSettingsDisableMic,
                'lockSettingsDisablePrivateChat'        => $videoconference->lockSettingsDisablePrivateChat,
                'lockSettingsDisablePublicChat'         => $videoconference->lockSettingsDisablePublicChat,
                'lockSettingsDisableNote'               => $videoconference->lockSettingsDisableNote,
                'lockSettingsLockedLayout'              => $videoconference->lockSettingsLockedLayout,
                'lockSettingsLockOnJoin'                => $videoconference->lockSettingsLockOnJoin,
                'lockSettingsLockOnJoinConfigurable'    => $videoconference->lockSettingsLockOnJoinConfigurable,
                'guestPolicy'                           => $videoconference->guestPolicy,
                'meetingKeepEvents'                     => $videoconference->meetingKeepEvents,
                'endWhenNoModerator'                    => $videoconference->endWhenNoModerator,
                'endWhenNoModeratorDelayInMinutes'      => $videoconference->endWhenNoModeratorDelayInMinutes,
                'meetingLayout'                         => $videoconference->meetingLayout,
                'learningDashboardCleanupDelayInMinutes' => $videoconference->learningDashboardCleanupDelayInMinutes,
                'allowModsToEjectCameras'               => $videoconference->allowModsToEjectCameras,
                'allowRequestsWithoutSession'           => $videoconference->allowRequestsWithoutSession,
                'userCameraCap'                         => $videoconference->userCameraCap,
            ]);
        } else {
            return (new $this->adapter())->join([
                    'meetingID' => $videoconference->meetingID,
                    'userName' =>  auth()->user()->fullName(),
                    'password' =>  $videoconference->attendeePW,
            ]);
        }

    }

    public function getPresentations($videoconference)
    {
        $presentations = [];
        foreach ($videoconference->media AS $medium){
            $presentations[] = [
                'link' => env('APP_URL'). $medium->path(),
                'fileName' => $medium->filename
            ];
        }
        //dump($presentations);
        return $presentations;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Videoconference  $videoconference
     * @return \Illuminate\Http\Response
     */
    public function edit(Videoconference $videoconference)
    {
        //abort_unless((\Gate::allows('group_edit') and $videoconference->isAccessible()), 403);


        return view('videoconference.edit')
            ->with(compact('videoconference'));
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
        $input = $this->validateRequest();
        abort_unless((\Gate::allows('videoconference_edit') and $videoconference->isAccessible()), 403);

        $callbackUrl = $input['endCallbackUrl'] ?? $input['callbackUrl'];
        if ($callbackUrl == null)
        {
            $callbackUrl == $input['logoutUrl'];
        }

        $videoconference->update([
            'meetingID' => $input['meetingID'] ?? $videoconference->meetingID,
            'meetingName' => $input['meetingName'] ?? $videoconference->meetingName,
            'attendeePW' => $input['attendeePW'] ?? $videoconference->attendeePW,
            'moderatorPW' => $input['moderatorPW'] ?? $videoconference->moderatorPW,
            'callbackUrl' => $input['callbackUrl'] ?? $videoconference->callbackUrl,
            'welcomeMessage' => $input['welcomeMessage'] ?? $videoconference->welcomeMessage,
            'dialNumber' => $input['dialNumber'] ?? $videoconference->dialNumber,
            'maxParticipants' => $input['maxParticipants'] ?? $videoconference->maxParticipants,
            'logoutUrl' => $input['logoutUrl'] ?? $videoconference->logoutUrl,
            'record' => $input['record'] ?? $videoconference->record,
            'duration' => $input['duration'] ?? $videoconference->duration,
            'isBreakout' => $input['isBreakout'] ?? $videoconference->isBreakout,
            'moderatorOnlyMessage' => $input['moderatorOnlyMessage'] ?? $videoconference->moderatorOnlyMessage,
            'autoStartRecording' => $input['autoStartRecording'] ?? $videoconference->autoStartRecording,
            'allowStartStopRecording' => $input['allowStartStopRecording'] ?? $videoconference->allowStartStopRecording,
            'bannerText' => $input['bannerText'] ?? $videoconference->bannerText,
            'bannerColor' => $input['bannerColor'] ?? $videoconference->bannerColor,
            'logo' => $input['logo'] ?? $videoconference->logo,
            'copyright' => $input['copyright'] ?? $videoconference->copyright,
            'muteOnStart' => $input['muteOnStart'] ?? $videoconference->muteOnStart,
            'allowModsToUnmuteUsers' => $input['allowModsToUnmuteUsers'] ?? $videoconference->allowModsToUnmuteUsers,
            'lockSettingsDisableCam' => $input['lockSettingsDisableCam'] ?? $videoconference->lockSettingsDisableCam,
            'lockSettingsDisableMic' => $input['lockSettingsDisableMic'] ?? $videoconference->lockSettingsDisableMic,
            'lockSettingsDisablePrivateChat' => $input['lockSettingsDisablePrivateChat'] ?? $videoconference->lockSettingsDisablePrivateChat,
            'lockSettingsDisablePublicChat' => $input['lockSettingsDisablePublicChat'] ?? $videoconference->lockSettingsDisablePublicChat,
            'lockSettingsDisableNote' => $input['lockSettingsDisableNote'] ?? $videoconference->lockSettingsDisableNote,
            'lockSettingsLockedLayout' => $input['lockSettingsLockedLayout'] ?? $videoconference->lockSettingsLockedLayout,
            'lockSettingsLockOnJoin' => $input['lockSettingsLockOnJoin'] ?? $videoconference->lockSettingsLockOnJoin,
            'lockSettingsLockOnJoinConfigurable' => $input['lockSettingsLockOnJoinConfigurable'] ?? $videoconference->lockSettingsLockOnJoinConfigurable,
            'guestPolicy' => $input['guestPolicy'] ?? $videoconference->guestPolicy,
            'meetingKeepEvents' => $input['meetingKeepEvents'] ?? $videoconference->meetingKeepEvents,
            'endWhenNoModerator' => $input['endWhenNoModerator'] ?? $videoconference->endWhenNoModerator,
            'endWhenNoModeratorDelayInMinutes' => $input['endWhenNoModeratorDelayInMinutes'] ?? $videoconference->endWhenNoModeratorDelayInMinutes,
            'meetingLayout' => $input['meetingLayout'] ?? $videoconference->meetingLayout,
            'learningDashboardCleanupDelayInMinutes' => $input['learningDashboardCleanupDelayInMinutes'] ?? $videoconference->learningDashboardCleanupDelayInMinutes,
            'allowModsToEjectCameras' => $input['allowModsToEjectCameras'] ?? $videoconference->allowModsToEjectCameras,
            'allowRequestsWithoutSession' => $input['allowRequestsWithoutSession'] ?? $videoconference->allowRequestsWithoutSession,
            'userCameraCap' => $input['userCameraCap'] ?? $videoconference->userCameraCap,
            'owner_id' => auth()->user()->id,
        ]);
        $videoconference->save();

        return $videoconference;

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
            'id' => 'sometimes|integer',
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
            'callbackUrl' => 'sometimes',
            'welcomeMessage' => 'sometimes|string|nullable',
            'dialNumber' => 'sometimes',
            'maxParticipants' => 'sometimes|integer',
            'logoutUrl' => 'sometimes|nullable|url',
            'record' => 'sometimes|boolean',
            'duration' => 'sometimes|integer',
            'isBreakout' => 'sometimes|boolean',
            'moderatorOnlyMessage' => 'sometimes|string|nullable',
            'autoStartRecording' => 'sometimes|boolean',
            'allowStartStopRecording' => 'sometimes|boolean',
            'bannerText' => 'sometimes|string|nullable',
            'bannerColor' => 'sometimes|string',
            'logo' => 'sometimes|nullable|url',
            'copyright' => 'sometimes|string|nullable',
            'muteOnStart' => 'sometimes|boolean',
            'allowModsToUnmuteUsers' => 'sometimes|boolean',
            'lockSettingsDisableCam' => 'sometimes|boolean',
            'lockSettingsDisableMic' => 'sometimes|boolean',
            'lockSettingsDisablePrivateChat' => 'sometimes|boolean',
            'lockSettingsDisablePublicChat' => 'sometimes|boolean',
            'lockSettingsDisableNote' => 'sometimes|boolean',
            'lockSettingsLockedLayout' => 'sometimes|boolean',
            'lockSettingsLockOnJoin' => 'sometimes|boolean',
            'lockSettingsLockOnJoinConfigurable' => 'sometimes|boolean',
            'guestPolicy' => 'sometimes|string',
            'meetingKeepEvents' => 'sometimes|boolean',
            'endWhenNoModerator' => 'sometimes|boolean',
            'endWhenNoModeratorDelayInMinutes' => 'sometimes|integer',
            'meetingLayout' => 'sometimes|string',
            'learningDashboardCleanupDelayInMinutes' => 'sometimes|integer',
            'allowModsToEjectCameras' => 'sometimes|boolean',
            'allowRequestsWithoutSession' => 'sometimes|boolean',
            'userCameraCap' => 'sometimes|integer',
            'getRaw' => 'sometimes',
            'hooksID' => 'sometimes',
            'subscribable_type' => 'sometimes|string',
            'subscribable_id'   => 'sometimes|integer',
        ]);
    }

}

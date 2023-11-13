<?php

namespace App\Http\Controllers;

use App\Organization;
use App\User;
use \App\Http\Controllers\ShareTokenController;
use App\Videoconference;
use App\VideoconferenceSubscription;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
    public function servers()
    {
        abort_unless(\Gate::allows('videoconference_create'), 403);

        $servers = [];
        $i = 1;
        foreach (config('bigbluebutton.servers') as $server) {
            $servers[] = [
                "server"  => "server$i",
                "BBB_SERVER_NAME"  => $server['BBB_SERVER_NAME'],
                ];
            $i++;
        }

        if (request()->wantsJson()) {
            return $servers;
        }

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

    public function userVideoconferences($withOwned = true, $user = null)
    {
        if ($user == null)
        {
            $user = auth()->user();
        }
        $userCanSee = $user->videoconferences;

        foreach ($user->groups as $group) {
            $userCanSee = $userCanSee->merge($group->videoconferences);
        }
        $organization = Organization::find($user->current_organization_id)->videoconferences;
        $userCanSee = $userCanSee->merge($organization);

        if ($withOwned)
        {
            $owned = Videoconference::where('owner_id', $user->id)->get();
            $userCanSee = $userCanSee->merge($owned);
        }

        return $userCanSee->unique();
    }

    public function list(Request $request)
    {
        abort_unless(\Gate::allows('videoconference_access'), 403);

        switch ($request->filter)
        {
            case 'owner':            $videoconferences = Videoconference::where('owner_id', auth()->user()->id)->get();
                break;
            case 'shared_with_me':   $videoconferences = $this->userVideoconferences(false);
                break;
            case 'shared_by_me':     $videoconferences = Videoconference::where('owner_id', auth()->user()->id)->whereHas('subscriptions')->get();
                break;
            case 'all':
            default:                 $videoconferences = $this->userVideoconferences();
                break;
        }

        return empty($videoconferences) ? '' : DataTables::of($videoconferences)
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
        abort_unless(/*\Gate::allows('videoconference_create') AND */is_admin(), 403);

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

        $conference =  (new $this->adapter())->create([
            'meetingID'     => $input['meetingID'] ?? Str::uuid(),
            'meetingName'   => $input['meetingName'],
            'attendeePW'    => $input['attendeePW'],
            'moderatorPW'   => $input['moderatorPW'],
            'logoutUrl'     => $input['logoutUrl'],
            'server'        => $input['server'] ?? 'server1'
        ]);

        $videoconference = Videoconference::updateOrCreate([
            'meetingID'         => $conference['meetingID'],
            'meetingName'       => $input['meetingName'],
            'attendeePW'        => $conference['attendeePW'],
            'moderatorPW'       => $conference['moderatorPW'],
            'endCallbackUrl'    => env('APP_URL') . '/videoconferences/endCallback?meetingID=' . $conference['meetingID'],
            'owner_id'          => auth()->user()->id,
            'welcomeMessage'    => $input['welcomeMessage'] ?? config('bigbluebutton.create.welcomeMessage'),
            'dialNumber'        => $input['dialNumber'] ?? config('bigbluebutton.create.dialNumber'),
            'maxParticipants'   => $input['maxParticipants'] ?? config('bigbluebutton.create.maxParticipants'),
            'logoutUrl'         => $input['logoutUrl'] ?? config('bigbluebutton.create.logoutUrl'),
            'record'            => $input['record'] ?? config('bigbluebutton.create.record'),
            'duration'          => $input['duration'] ?? config('bigbluebutton.create.duration'),
            'isBreakout'        => $input['isBreakout'] ?? config('bigbluebutton.create.isBreakout'),
            'moderatorOnlyMessage'      => $input['moderatorOnlyMessage'] ?? config('bigbluebutton.create.moderatorOnlyMessage'),
            'autoStartRecording'        => $input['autoStartRecording'] ?? config('bigbluebutton.create.autoStartRecording'),
            'allowStartStopRecording'   => $input['allowStartStopRecording'] ?? config('bigbluebutton.create.allowStartStopRecording'),
            'bannerText'        => $input['bannerText'] ?? config('bigbluebutton.create.bannerText'),
            'bannerColor'       => $input['bannerColor'] ?? config('bigbluebutton.create.bannerColor'),
            'logo'              => $input['logo'] ?? config('bigbluebutton.create.logo'),
            'copyright'         => $input['copyright'] ?? config('bigbluebutton.create.copyright'),
            'muteOnStart'       => $input['muteOnStart'] ?? config('bigbluebutton.create.muteOnStart'),
            'allowModsToUnmuteUsers' => $input['allowModsToUnmuteUsers'] ?? config('bigbluebutton.create.allowModsToUnmuteUsers'),
            'lockSettingsDisableCam' => $input['lockSettingsDisableCam'] ?? config('bigbluebutton.create.lockSettingsDisableCam'),
            'lockSettingsDisableMic' => $input['lockSettingsDisableMic'] ?? config('bigbluebutton.create.lockSettingsDisableMic'),
            'lockSettingsDisablePrivateChat' => $input['lockSettingsDisablePrivateChat'] ?? config('bigbluebutton.create.lockSettingsDisablePrivateChat'),
            'lockSettingsDisablePublicChat' => $input['lockSettingsDisablePublicChat'] ?? config('bigbluebutton.create.lockSettingsDisablePublicChat'),
            'lockSettingsDisableNote' => $input['lockSettingsDisableNote'] ?? config('bigbluebutton.create.lockSettingsDisableNote'),
            'lockSettingsLockedLayout' => $input['lockSettingsLockedLayout'] ?? config('bigbluebutton.create.lockSettingsLockedLayout'),
            'lockSettingsLockOnJoin' => $input['lockSettingsLockOnJoin'] ?? config('bigbluebutton.create.lockSettingsLockOnJoin'),
            'lockSettingsLockOnJoinConfigurable' => $input['lockSettingsLockOnJoinConfigurable'] ?? config('bigbluebutton.create.lockSettingsLockOnJoinConfigurable'),
            'guestPolicy' => $input['guestPolicy'] ?? config('bigbluebutton.create.guestPolicy'),
            'meetingKeepEvents' => $input['meetingKeepEvents'] ?? config('bigbluebutton.create.meetingKeepEvents'),
            'endWhenNoModerator' => $input['endWhenNoModerator'] ?? config('bigbluebutton.create.endWhenNoModerator'),
            'endWhenNoModeratorDelayInMinutes' => $input['endWhenNoModeratorDelayInMinutes'] ?? config('bigbluebutton.create.endWhenNoModeratorDelayInMinutes'),
            'meetingLayout' => $input['meetingLayout'] ?? config('bigbluebutton.create.meetingLayout'),
            'learningDashboardCleanupDelayInMinutes' => $input['learningDashboardCleanupDelayInMinutes'] ?? config('bigbluebutton.create.learningDashboardCleanupDelayInMinutes'),
            'allowModsToEjectCameras' => $input['allowModsToEjectCameras'] ?? config('bigbluebutton.create.allowModsToEjectCameras'),
            'allowRequestsWithoutSession' => $input['allowRequestsWithoutSession'] ?? config('bigbluebutton.create.allowRequestsWithoutSession'),
            'allJoinAsModerator' => $input['allJoinAsModerator'] ?? false,
            'userCameraCap' => $input['userCameraCap'] ?? config('bigbluebutton.create.userCameraCap'),
            'medium_id' => $input['medium_id'] ?? null,
            'webcamsOnlyForModerator' => $input['webcamsOnlyForModerator'] ?? config('bigbluebutton.create.webcamsOnlyForModerator'),
            'anyoneCanStart' => $input['anyoneCanStart'] ?? false,
            'server' => $input['server'] ?? 'server1',
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
    public function show(Videoconference $videoconference, $editable = null)
    {
        if (Auth::user() == null) {       //if no user is authenticated authenticate guest
            LogController::set('guestLogin');
            LogController::setStatistics();
            Auth::loginUsingId((env('GUEST_USER')), true);
        }
        $input = $this->validateRequest();

        abort_unless((
            $videoconference->attendeePW == isset($input['attendeePW']) ? $input['attendeePW'] : null
            OR
            $videoconference->moderatorPW == isset($input['moderatorPW'])? $input['moderatorPW'] : null
        )
        OR
        $videoconference->isAccessible(),
        403);

        $videoconference = $videoconference->withoutRelations(['subscriptions'])->load(['media.license', 'owner']);

        if ($this->isModerator($videoconference) OR ($videoconference->moderatorPW == isset($input['moderatorPW']) ? $input['moderatorPW'] : null))
        {
            $videoconference->editable= true; //hack moderation flag
        }
        else
        {
            $videoconference->editable= $editable;
        }
        //dump($videoconference);
        return view('videoconference.show')
            ->with(compact('videoconference'));
    }

    private function isModerator(Videoconference $videoconference)
    {
        if (
            $videoconference->subscriptions->where('subscribable_type', "App\User")
                ->where('editable', 1)
                ->whereIn('subscribable_id', auth()->user()->id)->isNotEmpty()
            or $videoconference->subscriptions->where('subscribable_type', "App\Group")
                    ->where('editable', 1)
                    ->whereIn('subscribable_id', auth()->user()->groups->pluck('id'))->isNotEmpty()
            or $videoconference->subscriptions->where('subscribable_type', "App\Organization")
                ->where('editable', 1)
                ->whereIn('subscribable_id', auth()->user()->current_organization_id)->isNotEmpty()
            or ((env('GUEST_USER') != null)
                ? $videoconference->subscriptions->where('subscribable_type', "App\User")
                    ->where('editable', 1)
                    ->whereIn('subscribable_id', User::find(env('GUEST_USER'))->id)->isNotEmpty()
                : false) //or enrolled via guest
        )
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function start(Videoconference $videoconference)
    {
        $input = $this->validateRequest();
        $moderatorPW =  $input['moderatorPW'];
        $attendeePW = $input['attendeePW'];
        abort_unless((
                $videoconference->attendeePW == $attendeePW
                OR
                $videoconference->moderatorPW == $moderatorPW
            )
            OR
            $videoconference->isAccessible(),
            403);

        $userName = auth()->user()->fullName();

        if (
            (auth()->user()->role()->id == 8) || ($input['userName'] != $userName)
        )
        {
            $userName = $input['userName'] ?? auth()->user()->fullName();
        }

        LogController::set(get_class($this).'@'.__FUNCTION__, date('d.m.Y'));

        $moderatorTextPostfix = '<br/>Um jemanden als <b>Moderator:in</b> zur Konferenz einzuladen, schicken Sie diesen Link: <a href="'. env('APP_URL') . '/videoconferences/' . $videoconference->id . '/startWithPw?moderatorPW=' . $videoconference->moderatorPW
        . '">'. env('APP_URL') . '/videoconferences/' . $videoconference->id . '/startWithPw?moderatorPW=' . $videoconference->moderatorPW
            . '</a><br/><br/> Um jemanden als <b>Teilnehmer:in</b> zur Konferenz einzuladen, schicken Sie diesen Link:  <a href="'. env('APP_URL') . '/videoconferences/' . $videoconference->id . '/startWithPw?moderatorPW=' . $videoconference->moderatorPW
            . '">'. env('APP_URL') . '/videoconferences/' . $videoconference->id . '/startWithPw?attendeePW=' . $videoconference->attendeePW
            . '</a>';

        $adapter = new $this->adapter();
        if ((auth()->user()->id == $videoconference->owner_id) || ($videoconference->allJoinAsModerator == true) || $this->isModerator($videoconference) === true || $videoconference->moderatorPW == $moderatorPW)
        {
            return $adapter->start([
                'meetingID'                             => $videoconference->meetingID,
                'meetingName'                           => $videoconference->meetingName,
                'attendeePW'                            => $videoconference->attendeePW,
                'moderatorPW'                           => $videoconference->moderatorPW,
                'presentation'                          => $this->getPresentations($videoconference),
                'userName'                              => $userName,
                'endCallbackUrl'                        => $videoconference->endCallbackUrl,
                'welcomeMessage'                        => nl2br($videoconference->welcomeMessage),
                'dialNumber'                            => $videoconference->dialNumber,
                'maxParticipants'                       => $videoconference->maxParticipants,
                'logoutUrl'                             => $videoconference->logoutUrl,
                'record'                                => $videoconference->record,
                'duration'                              => $videoconference->duration,
                'isBreakout'                            => $videoconference->isBreakout,
                'moderatorOnlyMessage'                  => nl2br($videoconference->moderatorOnlyMessage) . $moderatorTextPostfix,
                'autoStartRecording'                    => $videoconference->autoStartRecording,
                'allowStartStopRecording'               => $videoconference->allowStartStopRecording,
                'bannerText'                            => $videoconference->bannerText ?? null,
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
                'server'                                => $videoconference->server ?? 'server1'
            ]);
        } else {
            if (!$adapter->isMeetingRunning([
                'server' => $videoconference->server,
                'meetingID' => $videoconference->meetingID
                ])
            )
            {

                //meeting not running, start
                 $adapter->start([
                    'meetingID'                             => $videoconference->meetingID,
                    'meetingName'                           => $videoconference->meetingName,
                    'attendeePW'                            => $videoconference->attendeePW,
                     'moderatorPW'                           => $videoconference->moderatorPW,
                    'presentation'                          => $this->getPresentations($videoconference),
                    'userName'                              => $userName,
                    'endCallbackUrl'                        => $videoconference->endCallbackUrl,
                    'welcomeMessage'                        => nl2br($videoconference->welcomeMessage),
                    'dialNumber'                            => $videoconference->dialNumber,
                    'maxParticipants'                       => $videoconference->maxParticipants,
                    'logoutUrl'                             => $videoconference->logoutUrl,
                    'record'                                => $videoconference->record,
                    'duration'                              => $videoconference->duration,
                    'isBreakout'                            => $videoconference->isBreakout,
                    'moderatorOnlyMessage'                  => nl2br($videoconference->moderatorOnlyMessage),
                    'autoStartRecording'                    => $videoconference->autoStartRecording,
                    'allowStartStopRecording'               => $videoconference->allowStartStopRecording,
                    'bannerText'                            => $videoconference->bannerText ?? null,
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
                    'server'                                => $videoconference->server ?? 'server1'
                ]);
            }
            //join as guest

            return $adapter->join([
                'meetingID' => $videoconference->meetingID,
                'userName'  => $userName,
                'password'  => $videoconference->attendeePW,
                'server'    => $videoconference->server ?? 'server1'
            ]);

        }
    }

    public function getStatus(Videoconference $videoconference)
    {
        $adapter = new $this->adapter();
        if (request()->wantsJson()) {
            if ($adapter->isMeetingRunning([
                'server'    => $videoconference->server ?? 'server1',
                'meetingID' => $videoconference->meetingID
            ]))
            {
                return ['videoconference' => $videoconference->path()];
            } else
            {
                return ['videoconference' => false];
            }
        }
    }

    public function getPresentations($videoconference)
    {
        $presentations = [];
        foreach ($videoconference->media AS $medium){
            $presentations[] = [
                'link' => ($medium->mime_type == 'url') ? $medium->path : env('APP_URL'). $medium->path(),
                'fileName' => $medium->title
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
        abort_unless((/*\Gate::allows('group_edit') and $videoconference->isAccessible() AND */is_admin()), 403);

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

        $videoconference->update([
            'meetingID' => $input['meetingID'] ?? $videoconference->meetingID,
            'meetingName' => $input['meetingName'] ?? $videoconference->meetingName,
            'attendeePW' => $input['attendeePW'] ?? $videoconference->attendeePW,
            'moderatorPW' => $input['moderatorPW'] ?? $videoconference->moderatorPW,
            'endCallbackUrl' => $input['endCallbackUrl'] ?? $videoconference->endCallbackUrl,
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
            'allJoinAsModerator' => $input['allJoinAsModerator'] ?? $videoconference->allJoinAsModerator,
            'userCameraCap' => $input['userCameraCap'] ?? $videoconference->userCameraCap,
            'medium_id' => $input['medium_id'] ?? null,
            'webcamsOnlyForModerator' => $input['webcamsOnlyForModerator'] ?? $videoconference->webcamsOnlyForModerator,
            'anyoneCanStart' => $input['anyoneCanStart'] ?? $videoconference->anyoneCanStart,

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

    public function getVideoconferenceByToken(Videoconference $videoconference, Request $request)
    {
        if (Auth::user() == null) {       //if no user is authenticated authenticate guest
            LogController::set('guestLogin');
            LogController::setStatistics();
            Auth::loginUsingId((env('GUEST_USER')), true);
        }

        $input = $this->validateRequest();

        $subscription = VideoconferenceSubscription::where('sharing_token',$input['sharing_token'] )->get()->first();
        if ($subscription->due_date) {
            $now = Carbon::now();
            $due_date = Carbon::parse($subscription->due_date);
            if ($due_date < $now) {
                abort(410, 'Dieser Link ist nicht mehr gültig');
            }
        }

        return $this->show($videoconference, $subscription->editable);

    }

    public function endCallback(Request $request)
    {
        $input = $this->validateRequest();
        $videoconference = Videoconference::where('meetingID', $input['meetingID'])->get()->first();

        $adapter = new $this->adapter();
        $info = $adapter->getMeetingInfo(
            [
                'meetingID' => $videoconference->meetingID,
                'moderatorPW' => $videoconference->moderatorPW
            ]
        );
        //dump($info['participantCount']);
        LogController::set(get_class($this).'@'.__FUNCTION__.'->participantCount', $videoconference->meetingID, $info['participantCount']);

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
            'allJoinAsModerator' => 'sometimes|boolean',
            'userCameraCap' => 'sometimes|integer',
            'getRaw' => 'sometimes',
            'hooksID' => 'sometimes',
            'subscribable_type' => 'sometimes|string',
            'subscribable_id'   => 'sometimes|integer',
            'sharing_token' => 'sometimes',
            'medium_id' => 'sometimes',
            'webcamsOnlyForModerator' => 'sometimes|boolean',
            'anyoneCanStart' => 'sometimes|boolean',
            'server' => 'sometimes|string'
        ]);
    }

}

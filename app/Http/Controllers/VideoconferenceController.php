<?php

namespace App\Http\Controllers;

use App\Interfaces\VideoconferenceInterface;
use App\Services\Regex;
use App\User;
use App\Videoconference;
use App\VideoconferenceSubscription;
use Bigbluebutton;
use Carbon\Carbon;
use Gate;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class VideoconferenceController extends Controller
{
    private string $adapter;

    public function __construct(){
        $this->adapter = config('app.videoconference_adapter');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function servers()
    {
        abort_unless(Gate::allows('videoconference_create'), 403);

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
     * @return View|JsonResponse
     */
    public function index()
    {
        abort_unless(Gate::allows('videoconference_access') and auth()->user()->id != config('app.guest_user_id'), 403);

        if (request()->wantsJson()) {
            return getEntriesForSelect2ByCollection(
                getSubscribedModels(Videoconference::class),
                'videoconferences.',
                ['meetingName', 'medium_id'],
                'meetingName',
                "meetingName",
            );
        }

        return view('videoconference.index');
    }

    public function list(Request $request): JsonResponse
    {
        abort_unless(Gate::allows('videoconference_access') and auth()->user()->id != config('app.guest_user_id'), 403);

        $videoconferences = Videoconference::query();

        return getDataTableWithEntries($videoconferences);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        abort(403);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Videoconference|void
     */
    public function store()
    {
        abort_unless(Gate::allows('videoconference_create'), 403);

        $input = $this->validateRequest();
        request()->validate([
            'meetingName' => 'required|string',
        ]);

        $meetingID = $input['meetingID'] ?? Str::uuid();
        $videoconference = Videoconference::updateOrCreate([
            'meetingID'         => $meetingID,
            'meetingName'       => $input['meetingName'],
            'attendeePW'        => $input['attendeePW'] ?? Hash::make(Str::random(8)),
            'moderatorPW'       => $input['moderatorPW'] ??  Hash::make(Str::random(8)),
            'endCallbackUrl'    => config('app.url') . '/videoconferences/endCallback?meetingID=' . $meetingID,
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

        $createMeeting = Bigbluebutton::server( $videoconference->server)->initCreateMeeting($videoconference->toArray());
        $conf = Bigbluebutton::server($videoconference->server)->create($createMeeting);

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
            return $videoconference;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Videoconference $videoconference
     * @param bool            $editable
     * @param null            $token
     * @return \Illuminate\View\View
     */
    public function show(Videoconference $videoconference, $editable = false, $token = null)
    {
        $input = $this->validateRequest();
        $attendeePW = isset($input['attendeePW']) ? $input['attendeePW'] : null;
        $moderatorPW = isset($input['moderatorPW']) ? $input['moderatorPW'] : null;

        abort_unless((
            $videoconference->attendeePW == $attendeePW
            OR $videoconference->moderatorPW == $moderatorPW
        )
        OR $videoconference->isAccessible($token),
        403, 'global.videoconference.access_denied');

        $videoconference = $videoconference->withoutRelations(['subscriptions'])->load(['media.license', 'owner']);

        if ($this->isModerator($videoconference) OR ($videoconference->moderatorPW == $moderatorPW))
        {
            $videoconference->editable = true; //hack moderation flag
        }
        else
        {
            $videoconference->editable = $editable;
        }

        return view('videoconference.show')
            ->with(compact('videoconference'));
    }

    private function isModerator(Videoconference $videoconference) // todo: -> better wording canStart -> means not is moderator
    {
        if ($videoconference->subscriptions->where('subscribable_type', "App\User")
                ->where('editable', 1)
                ->whereIn('subscribable_id', auth()->user()->id)->isNotEmpty()
            || $videoconference->subscriptions->where('subscribable_type', "App\Group")
                ->where('editable', 1)
                ->whereIn('subscribable_id', auth()->user()->groups->pluck('id'))->isNotEmpty()
            || $videoconference->subscriptions->where('subscribable_type', "App\Organization")
                ->where('editable', 1)
                ->whereIn('subscribable_id', auth()->user()->current_organization_id)->isNotEmpty()
            || ((config('app.guest_user_id') != null)
                ? $videoconference->subscriptions->where('subscribable_type', "App\User")
                    ->where('editable', 1)
                    ->whereIn('subscribable_id', User::find(config('app.guest_user_id'))->id)->isNotEmpty()
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
        $token = $input['sharing_token'] ?? null;
        $moderatorPW =  $input['moderatorPW'] ?? '';
        $attendeePW = $input['attendeePW'] ?? $videoconference->attendeePW;
        abort_unless((
                $videoconference->attendeePW == $attendeePW   // start with attendeePW
                OR $videoconference->moderatorPW == $moderatorPW // start with moderatorPW
            )
            OR $videoconference->isAccessible($token),
            403, 'global.videoconference.access_denied');

        $userName = auth()->user()->fullName();

        if ((auth()->user()->role()->id == 8) || ($input['userName'] != $userName))
        {
            $userName = $input['userName'] ?? auth()->user()->fullName();
        }

        LogController::set(get_class($this).'@'.__FUNCTION__, date('d.m.Y'));

        $moderatorTextPostfix = '<br/>Um jemanden als <b>Moderator:in</b> zur Konferenz einzuladen, schicken Sie diesen Link: <a href="'. config('app.url') . '/videoconferences/' . $videoconference->id . '/startWithPw?moderatorPW=' . $videoconference->moderatorPW
            . '">'. config('app.url') . '/videoconferences/' . $videoconference->id . '/startWithPw?moderatorPW=' . $videoconference->moderatorPW
            . '</a><br/><br/> Um jemanden als <b>Teilnehmer:in</b> zur Konferenz einzuladen, schicken Sie diesen Link:  <a href="'. config('app.url') . '/videoconferences/' . $videoconference->id . '/startWithPw?moderatorPW=' . $videoconference->moderatorPW
            . '">'. config('app.url') . '/videoconferences/' . $videoconference->id . '/startWithPw?attendeePW=' . $videoconference->attendeePW
            . '</a>';

        /** @var VideoconferenceInterface $adapter */
        $adapter = new $this->adapter();

        //set proper pw
        if ((auth()->user()->id == $videoconference->owner_id) ||
            ($videoconference->allJoinAsModerator === true) ||
            $this->isModerator($videoconference) === true ||
            $videoconference->moderatorPW == $moderatorPW
        ) {
            $currentPW = $videoconference->moderatorPW;
        } else {
            $currentPW = $videoconference->attendeePW;
        }

        if (!$adapter->isMeetingRunning([
            'server' => $videoconference->server,
            'meetingID' => $videoconference->meetingID
        ])
        ) {

            if ((auth()->user()->id == $videoconference->owner_id) ||
                ($videoconference->allJoinAsModerator === true) ||
                $this->isModerator($videoconference) === true ||
                $videoconference->moderatorPW == $currentPW ||
                $videoconference->anyoneCanStart === true
            ){
                $conf =  $adapter->start([
                    'meetingID'                             => $videoconference->meetingID,
                    'meetingName'                           => $videoconference->meetingName,
                    'attendeePW'                            => $videoconference->attendeePW,
                    'moderatorPW'                           => $currentPW , // not always moderator
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
            }
        }

        return $adapter->join([
            'meetingID' => $videoconference->meetingID,
            'userName'  => $userName,
            'password'  => $currentPW,
            'server'    => $videoconference->server ?? 'server1'
        ]);
    }

    public function getStatus(Videoconference $videoconference)
    {
        /** @var VideoconferenceInterface $adapter */
        $adapter = new $this->adapter();
        if (request()->wantsJson()) {
            $isRunning = $adapter->isMeetingRunning([
                'meetingID' => $videoconference->meetingID,
                'server'    => $videoconference->server ?? 'server1',
            ]);
            if ($isRunning)
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
                'link' => ($medium->mime_type == 'url') ? $medium->path : config('app.url'). $medium->path(),
                'fileName' => $medium->title
            ];
        }
        //dump($presentations);
        return $presentations;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Videoconference $videoconference
     * @return Response
     */
    public function edit(Videoconference $videoconference)
    {
        abort(403);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request         $request
     * @param Videoconference $videoconference
     * @return Response
     */
    public function update(Request $request, Videoconference $videoconference)
    {
        abort_unless((Gate::allows('videoconference_edit') and $videoconference->isAccessible()), 403);
        $input = $this->validateRequest();

        //todo: check if guestPolicy is changed. -> if not use initCreateMeeting() ?
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
            'guestPolicy' => format_select_input($input['guestPolicy']) ?? $videoconference->guestPolicy,
            'meetingKeepEvents' => $input['meetingKeepEvents'] ?? $videoconference->meetingKeepEvents,
            'endWhenNoModerator' => $input['endWhenNoModerator'] ?? $videoconference->endWhenNoModerator,
            'endWhenNoModeratorDelayInMinutes' => $input['endWhenNoModeratorDelayInMinutes'] ?? $videoconference->endWhenNoModeratorDelayInMinutes,
            'meetingLayout' => format_select_input($input['meetingLayout']) ?? $videoconference->meetingLayout,
            'learningDashboardCleanupDelayInMinutes' => $input['learningDashboardCleanupDelayInMinutes'] ?? $videoconference->learningDashboardCleanupDelayInMinutes,
            'allowModsToEjectCameras' => $input['allowModsToEjectCameras'] ?? $videoconference->allowModsToEjectCameras,
            'allowRequestsWithoutSession' => $input['allowRequestsWithoutSession'] ?? $videoconference->allowRequestsWithoutSession,
            'allJoinAsModerator' => $input['allJoinAsModerator'] ?? $videoconference->allJoinAsModerator,
            'userCameraCap' => $input['userCameraCap'] ?? $videoconference->userCameraCap,
            'medium_id' => $input['medium_id'] ?? null,
            'webcamsOnlyForModerator' => $input['webcamsOnlyForModerator'] ?? $videoconference->webcamsOnlyForModerator,
            'anyoneCanStart' => $input['anyoneCanStart'] ?? $videoconference->anyoneCanStart,

            'owner_id' => is_admin() ? $input['owner_id'] : auth()->user()->id,
        ]);
        $videoconference->save();

        return $videoconference;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Videoconference $videoconference
     * @return Response
     */
    public function destroy(Videoconference $videoconference)
    {
        abort_unless(Gate::allows('videoconference_delete'), 403);

        $videoconference->subscriptions()->delete();

        if (request()->wantsJson()) {
            return ['message' => $videoconference->delete()];
        }

        return $videoconference->delete();
    }

    public function getVideoconferenceByToken(Videoconference $videoconference, Request $request)
    {
        $input = $this->validateRequest();

        $subscription = VideoconferenceSubscription::where('sharing_token',$input['sharing_token'] )->get()->first();

        if (!isset($subscription)) abort(410, 'global.token_deleted');

        if (isset($subscription->due_date)) {
            $now = Carbon::now();
            $due_date = Carbon::parse($subscription->due_date);
            if ($due_date < $now) {
                abort(410, 'global.token_expired');
            }
        }

        return $this->show($videoconference, $subscription->editable, $input['sharing_token']);
    }

    public function endCallback(Request $request)
    {
        /* this endpoint is accessible without authentification, only use validated input! */
        $input = $this->validateRequest();
        $videoconference = Videoconference::where('meetingID', $input['meetingID'])->get()->first();

        /** @var VideoconferenceInterface $adapter */
        $adapter = new $this->adapter();
        $info = $adapter->getMeetingInfo(
            [
                'meetingID' => $videoconference->meetingID,
                'moderatorPW' => $videoconference->moderatorPW,
                'server' => $videoconference->server
            ]
        );
        //dump($info['participantCount']);
        if (isset($info['participantCount']))
        {
            LogController::set(get_class($this).'@'.__FUNCTION__.'->participantCount', $videoconference->meetingID, $info['participantCount']);
        }
    }

    protected function validateRequest()
    {
        return request()->validate(
            [
                'id' => 'sometimes|nullable|integer',
                'sharing_token' => 'sometimes|string',
                'meetingID' => 'sometimes',
                'meetingName' => 'sometimes|max:191',
                'attendeePW' => 'sometimes|max:191',
                'moderatorPW' => 'sometimes|max:191',
                'presentation' => 'sometimes',
                'recordID' => 'sometimes',
                'state' => 'sometimes',
                'userName' => 'sometimes',
                'password' => 'sometimes',
                'endCallbackUrl' => 'sometimes|max:191',
                'welcomeMessage' => 'sometimes|string|nullable',
                'dialNumber' => 'sometimes',
                'maxParticipants' => 'sometimes|integer',
                'logoutUrl' => 'sometimes|nullable|url|max:191',
                'record' => 'sometimes|boolean',
                'duration' => 'sometimes|integer',
                'isBreakout' => 'sometimes|boolean',
                'moderatorOnlyMessage' => 'sometimes|string|nullable',
                'autoStartRecording' => 'sometimes|boolean',
                'allowStartStopRecording' => 'sometimes|boolean',
                'bannerText' => 'sometimes|string|max:512|nullable',
                'bannerColor' => ['sometimes', 'string', 'regex:' . Regex::HEX_COLOR->value],
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
                'guestPolicy' => ['sometimes', Rule::in(config('bigbluebutton.create.possibleGuestPolicies'))],
                'meetingKeepEvents' => 'sometimes|boolean',
                'endWhenNoModerator' => 'sometimes|boolean',
                'endWhenNoModeratorDelayInMinutes' => 'sometimes|integer',
                'meetingLayout' => ['sometimes', Rule::in(config('bigbluebutton.create.possibleMeetingLayout'))],
                'learningDashboardCleanupDelayInMinutes' => 'sometimes|integer',
                'allowModsToEjectCameras' => 'sometimes|boolean',
                'allowRequestsWithoutSession' => 'sometimes|boolean',
                'allJoinAsModerator' => 'sometimes|boolean',
                'userCameraCap' => 'sometimes|integer',
                'getRaw' => 'sometimes',
                'hooksID' => 'sometimes',
                'subscribable_type' => 'sometimes|string',
                'subscribable_id'   => 'sometimes|integer',
                'medium_id' => 'sometimes|integer|nullable',
                'webcamsOnlyForModerator' => 'sometimes|boolean',
                'anyoneCanStart' => 'sometimes|boolean',
                'server' => 'sometimes|string',
                'owner_id' => 'sometimes|integer|nullable',
            ],
            [
                'guestPolicy' => 'The :attribute must be one of these values: [' . implode(', ', config('bigbluebutton.create.possibleGuestPolicies')) . ']',
                'meetingLayout' => 'The :attribute must be one of these values: [' . implode(', ', config('bigbluebutton.create.possibleMeetingLayout')) . ']',
            ]
        );
    }
}

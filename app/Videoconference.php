<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 *   @OA\Schema(
 *      @OA\Xml(name="Videoconference"),
 *
 *      @OA\Property( property="id", type="integer"),
 *      @OA\Property( property="meetingID", type="string", format="uuid"),
 *      @OA\Property( property="meetingName", type="string"),
 *      @OA\Property( property="attendeePW", type="string"),
 *      @OA\Property( property="moderatorPW", type="string"),
 *      @OA\Property( property="endCallbackUrl", type="string", format="uri"),
 *      @OA\Property( property="owner_id", type="integer"),
 *      @OA\Property( property="welcomeMessage", type="string"),
 *      @OA\Property( property="dialNumber", type="string"),
 *      @OA\Property( property="maxParticipants", type="string"),
 *      @OA\Property( property="logoutUrl", type="string"),
 *      @OA\Property( property="record", type="boolean"),
 *      @OA\Property( property="duration", type="integer"),
 *      @OA\Property( property="isBreakout", type="boolean"),
 *      @OA\Property( property="moderatorOnlyMessage", type="string"),
 *      @OA\Property( property="autoStartRecording", type="boolean"),
 *      @OA\Property( property="allowStartStopRecording", type="boolean"),
 *      @OA\Property( property="bannerText", type="string"),
 *      @OA\Property( property="bannerColor", type="string"),
 *      @OA\Property( property="logo", type="string"),
 *      @OA\Property( property="copyright", type="string"),
 *      @OA\Property( property="muteOnStart", type="boolean"),
 *      @OA\Property( property="lockSettingsDisableCam", type="boolean"),
 *      @OA\Property( property="lockSettingsDisableMic", type="boolean"),
 *      @OA\Property( property="lockSettingsDisablePrivateChat", type="boolean"),
 *      @OA\Property( property="lockSettingsDisablePublicChat", type="boolean"),
 *      @OA\Property( property="lockSettingsDisableNote", type="boolean"),
 *      @OA\Property( property="lockSettingsLockedLayout", type="boolean"),
 *      @OA\Property( property="lockSettingsLockOnJoin", type="boolean"),
 *      @OA\Property( property="lockSettingsLockOnJoinConfigurable", type="boolean"),
 *      @OA\Property( property="guestPolicy", type="string"),
 *      @OA\Property( property="meetingKeepEvents", type="boolean"),
 *      @OA\Property( property="endWhenNoModerator", type="boolean"),
 *      @OA\Property( property="endWhenNoModeratorDelayInMinutes", type="integer"),
 *      @OA\Property( property="meetingLayout", type="string"),
 *      @OA\Property( property="learningDashboardCleanupDelayInMinutes", type="integer"),
 *      @OA\Property( property="allowModsToEjectCameras", type="boolean"),
 *      @OA\Property( property="allowRequestsWithoutSession", type="boolean"),
 *      @OA\Property( property="allJoinAsModerator", type="boolean"),
 *      @OA\Property( property="userCameraCap", type="integer"),
 *      @OA\Property( property="medium_id", type="integer"),
 *      @OA\Property( property="webcamsOnlyForModerator", type="boolean"),
 *      @OA\Property( property="anyoneCanStart", type="boolean"),
 *      @OA\Property( property="server", type="string"),
 *      @OA\Property( property="updated_at", type="string", format="date-time"),
 *      @OA\Property( property="created_at", type="string", format="date-time"),
 *   ),
 */
class Videoconference extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'meetingID',
        'meetingName',
        'attendeePW',
        'moderatorPW',
        'endCallbackUrl',
        'welcomeMessage',
        'dialNumber',
        'maxParticipants',
        'logoutUrl',
        'record',
        'duration',
        'isBreakout',
        'moderatorOnlyMessage',
        'autoStartRecording',
        'allowStartStopRecording',
        'bannerText',
        'bannerColor',
        'logo',
        'copyright',
        'muteOnStart',
        'lockSettingsDisableCam',
        'lockSettingsDisableMic',
        'lockSettingsDisablePrivateChat',
        'lockSettingsDisablePublicChat',
        'lockSettingsDisableNote',
        'lockSettingsLockedLayout',
        'lockSettingsLockOnJoin',
        'lockSettingsLockOnJoinConfigurable',
        'guestPolicy',
        'meetingKeepEvents',
        'endWhenNoModerator',
        'endWhenNoModeratorDelayInMinutes',
        'meetingLayout',
        'learningDashboardCleanupDelayInMinutes',
        'learningDashboardCleanupDelayInMinutes',
        'allowModsToEjectCameras',
        'allowRequestsWithoutSession',
        'userCameraCap',
        'allJoinAsModerator',
        'owner_id',
        'subscribable_type',
        'subscribable_id',
        'userName',
        'medium_id',
        'webcamsOnlyForModerator',
        'anyoneCanStart',
        'server'
    ];

    protected $casts = [
        'editable' => 'boolean',
        'record' => 'boolean',
        'isBreakout' => 'boolean',
        'autoStartRecording' => 'boolean',
        'allowStartStopRecording' => 'boolean',
        'muteOnStart' => 'boolean',
        'lockSettingsDisableCam' => 'boolean',
        'lockSettingsDisableMic' => 'boolean',
        'lockSettingsDisablePrivateChat' => 'boolean',
        'lockSettingsDisablePublicChat' => 'boolean',
        'lockSettingsDisableNote' => 'boolean',
        'lockSettingsLockedLayout' => 'boolean',
        'lockSettingsLockOnJoin' => 'boolean',
        'lockSettingsLockOnJoinConfigurable' => 'boolean',
        'meetingKeepEvents' => 'boolean',
        'endWhenNoModerator' => 'boolean',
        'allowModsToEjectCameras' => 'boolean',
        'allowRequestsWithoutSession' => 'boolean',
        'allJoinAsModerator' => 'boolean',
        'webcamsOnlyForModerator' => 'boolean',
        'anyoneCanStart' => 'boolean',
    ];

    public function path()
    {
        return "/videoconferences/{$this->id}";
    }

    public function subscriptions()
    {
        return $this->hasMany(VideoconferenceSubscription::class);
    }

    public function mediaSubscriptions()
    {
        return $this->morphMany('App\MediumSubscription', 'subscribable');
    }

    public function medium()
    {
        return $this->hasOne('App\Medium', 'id', 'medium_id');
    }

    public function media()
    {
        return $this->hasManyThrough(
            'App\Medium',
            'App\MediumSubscription',
            'subscribable_id', // Foreign key on medium_subscription table...
            'id', // Foreign key on medium table...
            'id', // Local key on videoconference  table...
            'medium_id' // Local key on medium_subscription table...
        )->where('subscribable_type', get_class($this));
    }

    public function owner()
    {
        return $this->hasOne('App\User', 'id', 'owner_id');
    }

    public function isAccessible($token = null)
    {
        if (
            auth()->user()->videoconferences->contains('id', $this->id) // user enrolled
            or $this->subscriptions->where('subscribable_type', "App\Group")->whereIn('subscribable_id', auth()->user()->groups->pluck('id'))->isNotEmpty() //user is enroled in group
            or $this->subscriptions->where('subscribable_type', "App\Organization")->whereIn('subscribable_id', auth()->user()->current_organization_id)->isNotEmpty() //user is enroled in group
            or ($token and $this->subscriptions->where('sharing_token', $token)->isNotEmpty()) // or has token
            or $this->owner_id == auth()->user()->id // or owner
            or is_admin() // or admin
        ) {
            return true;
        } else {
            return false;
        }
    }
}

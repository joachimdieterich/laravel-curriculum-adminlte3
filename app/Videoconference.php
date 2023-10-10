<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'anyoneCanStart'
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

    public function isAccessible()
    {

        if (
            auth()->user()->videoconferences->contains('id', $this->id) // user enrolled
            or ($this->subscriptions->where('subscribable_type', "App\Group")->whereIn('subscribable_id', auth()->user()->groups->pluck('id')))->isNotEmpty() //user is enroled in group
            or ($this->subscriptions->where('subscribable_type', "App\Organization")->whereIn('subscribable_id', auth()->user()->current_organization_id))->isNotEmpty() //user is enroled in group
            or ($this->owner_id == auth()->user()->id)            // or owner
            or ((env('GUEST_USER') != null) ? User::find(env('GUEST_USER'))->videoconferences->contains('id', $this->id) : false) //or allowed via guest
            or is_admin() // or admin
        ) {
            return true;
        } else {
            return false;
        }
    }
}

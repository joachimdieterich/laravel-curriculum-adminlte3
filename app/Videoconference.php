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
        'callbackUrl',
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
        'owner_id',
        'subscribable_type',
        'subscribable_id',
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

    public function isAccessible()
    {
        if (
            auth()->user()->videoconferences->contains('id', $this->id) // user enrolled
            or ($this->owner_id == auth()->user()->id)            // or owner
            or is_admin() // or admin
        ) {
            return true;
        } else {
            return false;
        }
    }
}

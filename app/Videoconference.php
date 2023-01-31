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
        'callbackURL',
        'owner_id',
        'subscribable_type',
        'subscribable_id',
    ];


    public function subscriptions()
    {
        return $this->hasMany(VideoconferenceSubscription::class);
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

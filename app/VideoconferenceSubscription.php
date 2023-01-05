<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoconferenceSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscribable_type',
        'subscribable_id',
        'videoconference_id',
        'editable',
        'owner_id', ];

    /**
     * Get the subscriber model.
     */
    public function subscribable()
    {
        return $this->morphTo();
    }

    public function videoconference()
    {
        return $this->belongsTo('App\Videoconference', 'videoconference_id', 'id');
    }

    public function isAccessible()
    {
        return $this->videoconference->isAccessible();
    }

}

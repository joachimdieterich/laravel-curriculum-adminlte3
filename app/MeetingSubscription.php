<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeetingSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscribable_type',
        'subscribable_id',
        'meeting_id',
        'due_date',
        'editable',
        'owner_id',
        'sharing_token',
        'title'
    ];

    protected $casts = [
        'editable' => 'boolean',
    ];

    /**
     * Get the subscriber model.
     */
    public function subscribable()
    {
        return $this->morphTo();
    }

    public function meeting()
    {
        return $this->belongsTo('App\Meeting', 'meeting_id', 'id');
    }

    public function isAccessible()
    {
        return $this->meeting->isAccessible();
    }
}

<?php

namespace App;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Mews\Purifier\Casts\CleanHtml;

class Meeting extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'uid',
        'access_token',
        'title',
        'subtitle',
        'description',
        'info',
        'speakers',
        'begin',
        'end',
        'status',
        'category',
        'target_group',
        'url',
        'provider',
        'medium_id',
        'owner_id',
        'livestream',
        'color'
    ];

    protected $casts = [
        'description' => CleanHtml::class,
        'info' => CleanHtml::class,
        'speakers' => CleanHtml::class,
        'livestream' => CleanHtml::class,
        'updated_at' => 'datetime',
        'created_at'  => 'datetime',
    ];

    /**
     * Prepare a date for array / JSON serialization.
     *
     * @param  \DateTimeInterface  $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function path()
    {
        return "/meetings/{$this->id}";
    }

    public function medium()
    {
        return $this->hasOne('App\Medium', 'id', 'medium_id');
    }
    public function dates()
    {
          return $this->hasMany(MeetingDate::class, 'meeting_id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(MeetingSubscription::class);
    }

    public function userSubscriptions()
    {
        return $this->hasMany(MeetingSubscription::class)
            ->where('subscribable_type', 'App\User');
    }

    public function groupSubscriptions()
    {
        return $this->hasMany(MeetingSubscription::class)
            ->where('subscribable_type', 'App\Group');
    }

    public function organizationSubscriptions()
    {
        return $this->hasMany(MeetingSubscription::class)
            ->where('subscribable_type', 'App\Organization');
    }

}

<?php

namespace App;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    ];

    protected $dates = [
        'updated_at',
        'created_at',
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

}

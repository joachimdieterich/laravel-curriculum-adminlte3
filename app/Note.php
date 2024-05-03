<?php

namespace App;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'content',
        'notable_id',
        'notable_type',
        'user_id',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    /*protected $hidden = [
        'notable_id',
        'notable_type',
    ];*/

    protected $casts = [
        'id' => 'integer',
        'notable_id' => 'integer',
        'user_id' => 'integer',
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

    public function notable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo('users');
    }
}

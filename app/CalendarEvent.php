<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;

class CalendarEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'groupId',
        'allDay',
        'start',
        'end',
        'startStr',
        'endStr',
        'title',
        'url',
        'ClassNames',
        'editable',
        'display',
        'overlap',
        'source',
        'owner_id'
    ];

    protected $dates = [
        'start',
        'end',
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
}

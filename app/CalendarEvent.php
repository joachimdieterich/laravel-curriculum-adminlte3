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

    protected $casts = [
        'start' => 'datetime',
        'end'  => 'datetime',
        'updated_at' => 'datetime',
        'created_at'  => 'datetime',
    ];

    /* protected $dates = [  --> change v.10
         'updated_at',
         'created_at',
     ];*/


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

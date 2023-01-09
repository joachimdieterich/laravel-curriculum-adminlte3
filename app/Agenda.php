<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agenda extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'meeting_date_id',
        'title',
        'description',
        'owner_id',
    ];

    public function path()
    {
        return "/agendas/{$this->id}";
    }

    public function date()
    {
        return $this->belongsTo(MeetingDate::class);
    }

    public function items()
    {
        return $this->hasMany(AgendaItem::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class);
    }

    public function isAccessible()
    {
        if (
            //Todo: add conditions
            is_admin() // or admin
        ) {
            return true;
        } else {
            return false;
        }
    }

    public static function boot() {
        parent::boot();

        static::deleting(function($items) {
            $items->delete(); //delete dates
        });
    }

}

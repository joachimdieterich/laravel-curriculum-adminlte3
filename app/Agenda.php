<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agenda extends Model
{
    use HasFactory;

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
}

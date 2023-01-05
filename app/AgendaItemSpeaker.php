<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgendaItemSpeaker extends Model
{
    protected $fillable = [
        'agenda_item_id',
        'user_id',
        'title',
    ];

    use HasFactory;

    public function agendaItem()
    {
        return $this->belongsTo('App\AgendaItem', 'agenda_item_id', 'id');
    }

    public function user()
    {
        return $this->hasOne('App\User', 'id', 'user_id');
    }

    public function isAccessible()
    {
        return $this->item->isAccessible();
    }
}

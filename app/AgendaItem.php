<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgendaItem extends Model
{
    use HasFactory;

    public function type()
    {
        return $this->belongsTo(AgendaItemType::class, 'agenda_item_type_id');
    }

    public function subscriptions()
    {
        return $this->hasMany(AgendaItemSubscription::class);
    }
}

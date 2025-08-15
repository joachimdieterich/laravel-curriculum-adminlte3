<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgendaItemSubscription extends Model
{
    protected $fillable = [
        'subscribable_type',
        'subscribable_id',
        'agenda_item_id',
        'editable',
        'owner_id'
    ];

    use HasFactory;
    /**
    * Get the subscriber model.
    */
    public function subscribable()
    {
        return $this->morphTo();
    }

    public function item()
    {
        return $this->belongsTo('App\AgendaItem', 'agenda_item_id', 'id');
    }

    public function isAccessible()
    {
        return $this->item->isAccessible();
    }
}


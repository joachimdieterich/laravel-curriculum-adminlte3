<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgendaItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'agenda_id',
        'agenda_item_type_id',
        'title',
        'subtitle',
        'description',
        'medium_id',
        'begin',
        'end',
        'order_id',
        'owner_id'
    ];

    public function agenda()
    {
        return $this->belongsTo(Agenda::class, 'agenda_id');
    }

    public function type()
    {
        return $this->belongsTo(AgendaItemType::class, 'agenda_item_type_id');
    }

    public function medium()
    {
        return $this->hasOne('App\Medium', 'id', 'medium_id');
    }

    public function media()
    {
        return $this->hasManyThrough(
            'App\Medium',
            'App\MediumSubscription',
            'subscribable_id', // Foreign key on medium_subscription table...
            'id', // Foreign key on medium table...
            'id', // Local key on curriculum table...
            'medium_id' // Local key on medium_subscription table...
        )->where('subscribable_type', get_class($this));
    }

    public function speakers()
    {
        return $this->hasMany(AgendaItemSpeaker::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(AgendaItemSubscription::class);
    }

    public function videoconferences()
    {
        return $this->hasManyThrough(
            'App\Videoconference',
            'App\VideoconferenceSubscription',
            'subscribable_id',
            'id',
            'id',
            'videoconference_id'
        )->where('subscribable_type', get_class($this));
    }

    public function isAccessible()
    {
        return $this->agenda->isAccessible();
    }

    public static function boot() {
        parent::boot();

        static::deleting(function($agendaItem) {
            $agendaItem->subscriptions()->delete(); //delete subscriptions on delete
        });
    }
}

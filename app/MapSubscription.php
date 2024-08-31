<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MapSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscribable_type',
        'subscribable_id',
        'map_id',
        'editable',
        'due_date',
        'title',
        'sharing_token',
        'owner_id'
    ];

    protected $casts = [
        'editable' => 'boolean',
    ];

    /**
     * Get the subscriber model.
     */
    public function subscribable()
    {
        return $this->morphTo();
    }

    public function map()
    {
        return $this->belongsTo('App\Map', 'map_id', 'id');
    }

    public function isAccessible()
    {
        return $this->videoconference->isAccessible();
    }
}

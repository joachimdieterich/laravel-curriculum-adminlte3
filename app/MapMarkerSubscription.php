<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MapMarkerSubscription extends Model
{
    use HasFactory;

    public mixed $editable;
    protected $fillable = [
        'subscribable_type',
        'subscribable_id',
        'map_marker_id',
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

    public function mapMarker()
    {
        return $this->belongsTo('App\MapMarker', 'map_marker_id', 'id');
    }

    public function isAccessible()
    {
        return $this->mapMarker()->isAccessible();
    }
}

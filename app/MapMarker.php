<?php

namespace App;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MapMarker extends Model
{
    use HasFactory;

    protected $dates = [
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

    public function type()
    {
        return $this->belongsTo(MapMarkerType::class);
    }

    public function category()
    {
        return $this->belongsTo(MapMarkerCategory::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class);
    }


}

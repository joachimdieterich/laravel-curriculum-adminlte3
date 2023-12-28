<?php

namespace App;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MapMarkerCategory extends Model
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

    public function markers()
    {
        return $this->hasMany(MapMarker::class);
    }

    public function children()
    {
        return $this->hasMany(self::class , 'parent_id');
    }

    public function parents()
    {
        return $this->belongsTo(self::class , 'parent_id');
    }
}

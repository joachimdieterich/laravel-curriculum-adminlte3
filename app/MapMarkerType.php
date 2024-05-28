<?php

namespace App;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MapMarkerType extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'color', 'css_icon', 'owner_id', 'created_at', 'updated_at'];

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

    public function path()
    {
        return "/mapMarkerTypes/{$this->id}";
    }

    public function markers()
    {
        return $this->hasMany(MapMarker::class);
    }
}

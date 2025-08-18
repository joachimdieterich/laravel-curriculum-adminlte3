<?php

namespace App;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Mews\Purifier\Casts\CleanHtml;

class MapMarker extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'title',
        'teaser_text',
        'description',
        'author',
        'type_id',
        'category_id',
        'map_id',
        'tags',
        'latitude',
        'longitude',
        'address',
        'url',
        'url_title',
        'owner_id',
    ];

    protected $casts = [
        'description' => CleanHtml::class, // cleans both when getting and setting the value
        'updated_at' => 'datetime',
        'created_at'  => 'datetime',
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

    public function media()
    {
        return $this->hasManyThrough(
            'App\Medium',
            'App\MediumSubscription',
            'subscribable_id', // Foreign key on medium_subscription table...
            'id', // Foreign key on medium table...
            'id', // Local key on enabling_objectives table...
            'medium_id' // Local key on medium_subscription table...
        )->where('subscribable_type', get_class($this));
    }

    public function mediaSubscriptions()
    {
        return $this->morphMany('App\MediumSubscription', 'subscribable');
    }

    public function subscriptions()
    {
        return $this->hasMany(MapMarkerSubscription::class);
    }

    public function isAccessible()
    {
//Todo: how to check if marker is accessible
        if (
           is_admin() // or admin
        ) {
            return true;
        } else {
            return false;
        }
    }

}

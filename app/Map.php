<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Mews\Purifier\Casts\CleanHtml;

class Map extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'title',
        'subtitle',
        'description',
        'tags',
        'type_id',
        'category_id',
        'border_url',
        'latitude',
        'longitude',
        'zoom',
        'color',
        'medium_id',
        'owner_id',
    ];

    protected $casts = [
        'description' => CleanHtml::class,
    ];

    public function path()
    {
        return "/videoconferences/{$this->id}";
    }

    public function type()
    {
        return $this->belongsTo(MapMarkerType::class);
    }

    public function category()
    {
        return $this->belongsTo(MapMarkerCategory::class);
    }

    public function markers()
    {
        return $this->hasMany(MapMarker::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(MapSubscription::class);
    }

    public function isAccessible()
    {

        if (
            auth()->user()->maps->contains('id', $this->id) // user enrolled
            or ($this->subscriptions->where('subscribable_type', "App\Group")->whereIn('subscribable_id', auth()->user()->groups->pluck('id')))->isNotEmpty() //user is enroled in group
            or ($this->subscriptions->where('subscribable_type', "App\Organization")->whereIn('subscribable_id', auth()->user()->current_organization_id))->isNotEmpty() //user is enroled in group
            or ($this->owner_id == auth()->user()->id)            // or owner
            or ((env('GUEST_USER') != null) ? User::find(env('GUEST_USER'))->maps->contains('id', $this->id) : false) //or allowed via guest
            or is_admin() // or admin
        ) {
            return true;
        } else {
            return false;
        }
    }

    public function isEditable($user_id = null, $token = null): bool
    {
        if ($token !== null) {
            return $this->subscriptions()->where('sharing_token', $token)->pluck('editable')->first();
        }
        if ($user_id == null) {
            $user_id = auth()->user()->id;
        }
        if ($user_id == $this->owner_id || is_admin()) return true;
        if ($user_id == env('GUEST_USER')) return false; // guests currently don't have edit rights

        return $this->subscriptions()->where('editable', 1)
            ->where(function ($query) use ($user_id) {
                // user subscription with edit rights
                $query->where([
                    'subscribable_type' => 'App\User',
                    'subscribable_id' => $user_id,
                ])
                // group subscription with edit rights
                ->orWhere(function ($query) use ($user_id) {
                    $query->where('subscribable_type', 'App\Group')
                        ->whereIn('subscribable_id', User::find($user_id)->groups()->pluck('groups.id'));
                })
                // organization subscription with edit rights
                ->orWhere(function ($query) use ($user_id) {
                    $query->where('subscribable_type', 'App\Organization')
                        ->whereIn('subscribable_id', User::find($user_id)->organizations()->pluck('organizations.id'));
                });
            })->count() > 0;
    }

    protected static function booted()
    {
        static::deleting(function ($map) {
            $map->subscriptions->each->delete();
            $map->markers->each->delete();
        });
    }
}
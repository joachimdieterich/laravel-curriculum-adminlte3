<?php

namespace App;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Logbook extends Model
{
    use HasFactory;

    protected $guarded = [];

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
        return "/logbooks/{$this->id}";
    }

    public function entries()
    {
        return $this->hasMany('App\LogbookEntry')->orderBy('begin', 'DESC');
    }

    public function subscriptions()
    {
        return $this->hasMany(LogbookSubscription::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class);
    }

    public function subscribe($model)
    {
        $subscription = LogbookSubscription::firstOrNew([
            'logbook_id' => $this->id,
            'subscribable_type' => get_class($model),
            'subscribable_id' => $model->id,
            'owner_id' => auth()->user()->id,
        ]);
        $subscription->save();

        return $subscription;
    }

    public function isAccessible()
    {
        if (
            auth()->user()->logbooks->contains('id', $this->id) //direct subscription
            or ($this->subscriptions->where('subscribable_type', "App\Group")->whereIn('subscribable_id', auth()->user()->groups->pluck('id')))->isNotEmpty() //user is enroled in group
            or ($this->subscriptions->where('subscribable_type', "App\Organization")->whereIn('subscribable_id', auth()->user()->current_organization_id))->isNotEmpty() //user is enroled in group
            or ($this->subscriptions->where('subscribable_type', "App\Course")->whereIn('subscribable_id', auth()->user()->currentGroupEnrolments->pluck('course_id')))->isNotEmpty()
            or (auth()->user()->id == $this->owner_id)           // user owns logbook
            or is_admin()
        ) {
            return true;
        } else {
            return false;
        }
    }
}

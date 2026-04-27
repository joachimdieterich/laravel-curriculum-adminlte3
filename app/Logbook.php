<?php

namespace App;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Logbook extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'updated_at' => 'datetime',
        'created_at'  => 'datetime',
    ];

    /* protected $dates = [  --> change v.10
         'updated_at',
         'created_at',
     ];*/

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
        return auth()->user()->logbooks->contains('id', $this->id) or is_admin();
    }
}
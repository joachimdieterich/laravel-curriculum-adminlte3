<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContactDetail extends Model
{
    protected $fillable = [
        'email',
        'phone',
        'mobile',
        'notes',
        'owner_id',
    ];

    public function path()
    {
        return route('contactdetails.show', $this->id);
    }

    public function subscriptions()
    {
        return $this->hasMany(ContactDetailSubscription::class);
    }

    public function subscribe($model)
    {
        $subscription = ContactDetailSubscription::firstOrNew([
            'contactdetail_id' => $this->id,
            'subscribable_type' => get_class($model),
            'subscribable_id' => $model->id,
        ]);
        $subscription->save();

        return $subscription;
    }

    public function owner()
    {
        return $this->hasOne('App\User', 'id', 'owner_id');
    }
}

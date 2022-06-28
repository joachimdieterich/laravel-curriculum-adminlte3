<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LmsReference extends Model
{
    protected $guarded = [];

    protected $casts = ['value' => 'array'];

    public function subscriptions()
    {
        return $this->hasMany(LmsReferenceSubscription::class);
    }
}

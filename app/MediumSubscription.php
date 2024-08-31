<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MediumSubscription extends Model
{
    protected $guarded = [];

    protected $casts = [
        'additional_data' => 'array',
        'editable' => 'boolean',
    ];

    public function medium()
    {
        return $this->hasOne('App\Medium', 'id', 'medium_id');
    }

    public function subscribable()
    {
        return $this->morphTo();
    }
}

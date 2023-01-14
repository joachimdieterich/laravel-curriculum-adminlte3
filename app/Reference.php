<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reference extends Model
{
    protected $guarded = [];

    protected $casts = ['id' => 'string']; //important to get id as unique string

    public $incrementing = false;

    public function subscribe($model, $sharing_level_id = 1, $visibility = true)
    {
        $subscribe = ReferenceSubscription::firstOrCreate([
            'reference_id' => $this->id,
            'referenceable_type' => get_class($model),
            'referenceable_id' => $model->id,
            'sharing_level_id' => $sharing_level_id,
            'visibility' => $visibility,
            'owner_id' => auth()->user()->id,
        ]);
    }

    public function subscriptions()
    {
        return $this->hasMany('App\ReferenceSubscription', 'reference_id', 'id');
    }
}

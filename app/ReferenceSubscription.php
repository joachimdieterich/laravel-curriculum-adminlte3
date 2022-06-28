<?php

namespace App;

use App\Helpers\HasManySiblings;
use Illuminate\Database\Eloquent\Model;

class ReferenceSubscription extends Model
{
    protected $guarded = [];

    protected $casts = ['reference_id' => 'string']; //important to get id as unique string

    public function getRouteKeyName()
    {
        return 'reference_id';
    }

    public function reference()
    {
        return $this->hasOne('App\Reference', 'id', 'reference_id');
    }

    public function referenceable()
    {
        return $this->morphTo();
    }

    public function siblings()
    {
        return $this->hasMany('App\ReferenceSubscription', 'reference_id', 'reference_id')->with(['reference', 'referenceable.curriculum.organizationType']);
        // return new HasManySiblings($this->newRelatedInstance(ReferenceSubscription::class)->newQuery(), $this, 'reference_id', 'reference_id');
    }
}

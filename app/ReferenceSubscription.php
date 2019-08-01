<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Helpers\HasManySiblings;

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
    
    /**
     * Get the owning commentable model.
     */
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

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LmsReferenceSubscription extends Model
{
    protected $fillable = [
        'subscribable_type',
        'subscribable_id',
        'lms_reference_id',
        'editable',
        'owner_id', ];

    protected $casts = [
        'editable' => 'boolean',
    ];
    /**
     * Get the subscriber model.
     */
    public function subscribable()
    {
        return $this->morphTo();
    }

    public function lms_reference()
    {
        return $this->belongsTo('App\LmsReference', 'lms_reference_id', 'id');
    }
}

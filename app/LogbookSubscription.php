<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LogbookSubscription extends Model
{
    protected $fillable = [
        'subscribable_type',
        'subscribable_id',
        'logbook_id',
        'editable',
        'owner_id'];

    public function subscribable()
    {
        return $this->morphTo();
    }

    public function logbook()
    {
        return $this->belongsTo('App\Logbook', 'logbook_id', 'id');
    }

    public function owner()
    {
        return $this->hasOne('App\User', 'id', 'owner_id');
    }

    public function isAccessible()
    {
        return $this->logbook->isAccessible();
    }
}

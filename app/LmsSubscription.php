<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LmsSubscription extends Model
{
    protected $guarded = [];
    protected $casts = ['value' => 'array'];
}

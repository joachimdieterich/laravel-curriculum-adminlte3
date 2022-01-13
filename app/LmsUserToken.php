<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LmsUserToken extends Model
{
    protected $fillable = [
        'token',
        'organization_id',
        'user_id'
    ];
}

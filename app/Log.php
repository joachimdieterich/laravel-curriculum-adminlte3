<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $fillable = [
        'key',
        'value',
        'counter',
        'created_at',
        'updated_at',
        'deleted_at',
    ];


}

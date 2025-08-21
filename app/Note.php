<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Mews\Purifier\Casts\CleanHtml;

class Note extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'content',
        'notable_id',
        'notable_type',
        'user_id',
    ];
    
    protected $casts = [
        'id' => 'integer',
        'content' => CleanHtml::class,
        'notable_id' => 'integer',
        'user_id' => 'integer',
    ];

    public function notable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo('users');
    }
}
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    /*protected $hidden = [
        'notable_id',
        'notable_type',
    ];*/

    protected $casts = [
        'id' => 'integer',
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

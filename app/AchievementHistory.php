<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AchievementHistory extends Model
{
    protected $fillable = [
        'achievement_id',
        'status',
        'owner_id',
        'created_at',
    ];

    protected $casts = [
        'status' => 'string',
        'created_at'  => 'datetime',
    ];

    public $timestamps = false;

    public function achievement(): BelongsTo
    {
        return $this->belongsTo(Achievement::class);
    }

    public function owner(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'owner_id')->select('id', 'firstname', 'lastname');
    }
}
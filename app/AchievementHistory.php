<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}
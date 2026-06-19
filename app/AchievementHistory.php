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
    ];

    protected $casts = [
        'status' => 'string',
        'created_at'  => 'datetime',
    ];

    const UPDATED_AT = null; // table doesn't have an 'updated_at'-column

    public function achievement(): BelongsTo
    {
        return $this->belongsTo(Achievement::class);
    }
}
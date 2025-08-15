<?php

namespace App;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Maize\Markable\Markable;
use Maize\Markable\Models\Like;

class KanbanItemComment extends Model
{
    use HasFactory, Markable;

    protected $casts = [
        'created_at' => 'datetime:d.m.Y H:i',
    ];

    protected $guarded = [];

    protected $dates = [
        'updated_at',
        'created_at',
    ];

    protected static $marks = [
        Like::class,
    ];


    /**
     * Prepare a date for array / JSON serialization.
     *
     * @param  \DateTimeInterface  $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function kanbanItem()
    {
        return $this->belongsTo(KanbanItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

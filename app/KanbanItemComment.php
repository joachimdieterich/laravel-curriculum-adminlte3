<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KanbanItemComment extends Model
{
    use HasFactory;

    protected $casts = [
        'created_at' => 'datetime:d.m.Y H:i',
    ];

    protected $guarded = [];

    public function kanbanItem()
    {
        return $this->belongsTo(KanbanItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

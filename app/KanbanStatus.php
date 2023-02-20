<?php

namespace App;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

class KanbanStatus extends Model
{
    protected $guarded = [];

    protected $dates = [
        'updated_at',
        'created_at',
    ];

    protected $casts = [
        'editable' => 'boolean',
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

    public function items()
    {
        return $this->hasMany('App\KanbanItem')->orderBy('order_id');
    }

    public function owner()
    {
        return $this->hasOne('App\User', 'id', 'owner_id');
    }

    public function kanban()
    {
        return $this->hasOne('App\Kanban', 'id', 'kanban_id');
    }

    public function isAccessible()
    {
        return $this->kanban->isAccessible();
    }
}

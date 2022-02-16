<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class KanbanItemSubscription extends Model
{
    protected $fillable = [
        'subscribable_type',
        'subscribable_id',
        'kanban_item_id',
        'editable',
        'owner_id'];
    /**
     * Get the subscriber model.
     */
    public function subscribable()
    {
        return $this->morphTo();
    }

    public function kanbanItem()
    {
        return $this->belongsTo('App\KanbanItem', 'kanban_item_id', 'id');
    }

    public function isAccessible()
    {
        return $this->kanbanItem->isAccessible();
    }
}

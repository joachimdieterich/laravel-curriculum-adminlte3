<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 *   @OA\Schema(
 *      @OA\Xml(name="KanbanSubscription"),
 *
 *      @OA\Property( property="id", type="integer"),
 *      @OA\Property( property="kanban_id", type="integer"),
 *      @OA\Property( property="subscribable_type", type="string"),
 *      @OA\Property( property="subscribable_id", type="integer"),
 *      @OA\Property( property="editable", type="boolean"),
 *      @OA\Property( property="owner_id", type="integer"),
 *      @OA\Property( property="created_at", type="string"),
 *      @OA\Property( property="updated_at", type="string"),
 *      @OA\Property( property="due_date", type="date"),
 *      @OA\Property( property="sharing_token", type="string"),
 *      @OA\Property( property="title", type="string"),
 *   ),
 */
class KanbanSubscription extends Model
{
    protected $fillable = [
        'subscribable_type',
        'subscribable_id',
        'kanban_id',
        'due_date',
        'editable',
        'owner_id',
        'sharing_token',
        'title'
    ];

    protected $casts = [
        'editable' => 'boolean',
    ];

    /**
     * Get the subscriber model.
     */
    public function subscribable()
    {
        return $this->morphTo();
    }

    public function kanban()
    {
        return $this->belongsTo('App\Kanban', 'kanban_id', 'id');
    }

    public function isAccessible()
    {
        return $this->kanban->isAccessible();
    }
}

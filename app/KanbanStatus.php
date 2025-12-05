<?php

namespace App;

use DateTimeInterface;
use Illuminate\Broadcasting\Channel;
use Illuminate\Database\Eloquent\BroadcastsEvents;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class KanbanStatus extends Model
{
    use BroadcastsEvents;

    protected $guarded = [];

    protected $casts = [
        'locked'        => 'boolean',
        'editable'      => 'boolean',
        'visibility'    => 'boolean',
        'updated_at'    => 'datetime',
        'created_at'    => 'datetime',
        'visible_from'  => 'datetime',
        'visible_until' => 'datetime',
    ];

    public function broadcastOn(): array
    {
        if (!env('WEBSOCKET_APP_ACTIVE', false)) {
            return [];
        }

        return [
            new Channel($this->broadcastChannel())
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'model' => $this->withRelations(),
        ];
    }

    /**
     * Prepare a date for array / JSON serialization.
     *
     * @param DateTimeInterface $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function items(): HasMany|KanbanStatus
    {
        return $this->hasMany('App\KanbanItem')->orderBy('order_id');
    }

    public function owner(): HasOne|KanbanStatus
    {
        return $this->hasOne('App\User', 'id', 'owner_id');
    }

    public function kanban(): HasOne|KanbanStatus
    {
        return $this->hasOne('App\Kanban', 'id', 'kanban_id');
    }

    /**
     * Accessor that mimics Eloquent dynamic property.
     *
     * @return Collection
     */
    public function getEditorsAttribute(): Collection
    {
        if (!$this->relationLoaded('editors')) {
            $layers = User::whereIn('id', $this->editors_ids)->get();

            $this->setRelation('editors', $layers);
        }

        return $this->getRelation('editors');
    }

    /**
     * Access editors relation query.
     *
     * @return Builder
     */
    public function editors(): Builder
    {
        return User::whereIn('id', $this->editors_ids);
    }

    /**
     * Accessor for editors_ids property.
     */
    public function getEditorsIdsAttribute($commaSeparatedIds): array
    {
        return explode(',', $commaSeparatedIds);
    }

    /**
     * Mutator for layer_ids property.
     *
     * @param array|string $ids
     * @return void
     */
    public function setEditorsIdsAttribute($ids): void
    {
        $this->attributes['editors_ids'] = is_string($ids) ? $ids : implode(
            ',',
            array_filter($ids)
        ); // array filter removes empty entries.
    }

    public function isAccessible(): bool
    {
        return $this->kanban->isAccessible();
    }

    public function isEditable($user = null, $sharing_token = null)
    {
        return $this->kanban->isEditable($user, $sharing_token);
    }

    public function withRelations(): Model|null
    {
        return $this
            ->with([
                'items' => function ($query) {
                    $query->where('kanban_id', $this->kanban_id)
                        ->with(['owner', 'mediaSubscriptions.medium'])
                        ->orderBy('order_id');
                },
                'items.subscriptions',
                'items.comments',
                'items.comments.likes',
                'items.comments.user',
                'items.likes',
            ])->where('id', $this->id)->get()->first();
    }

    protected static function booted()
    {
        static::deleting(function (KanbanStatus $kanbanStatus) {
            // each item needs to be deleted separately to trigger its booted function
            $kanbanStatus->items->each->delete();
        });
    }
}

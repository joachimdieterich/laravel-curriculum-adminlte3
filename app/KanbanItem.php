<?php

namespace App;

use App\Http\Controllers\MediumSubscriptionController;
use App\Services\Websocket\BroadcastsEvents;
use DateTimeInterface;
use Illuminate\Broadcasting\Channel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Maize\Markable\Markable;
use Mews\Purifier\Casts\CleanHtml;

class KanbanItem extends Model
{
    use BroadcastsEvents;
    use Markable;

    protected $guarded = [];

    protected $casts = [
        'description'   => CleanHtml::class, // cleans both when getting and setting the value
        'locked'        => 'boolean',
        'editable'      => 'boolean',
        'replace_links' => 'boolean',
        'visibility'    => 'boolean',
        'updated_at'    => 'datetime',
        'created_at'    => 'datetime',
        'due_date'      => 'datetime',
        'visible_from'  => 'datetime',
        'visible_until' => 'datetime',
    ];

    protected static array $marks = [
        Like::class,
    ];

    public function broadcastOn($event): array
    {
        if (! config('broadcasting.active')) {
            return [];
        }

        $defaultChannels = [
            new Channel($this->broadcastChannel()),
        ];

        $diff                = $this->getDirty();
        $updatedAtColumnName = $this->getUpdatedAtColumn();

        // If the only changed column is the updated_at (touch) just proceed normal
        if (count($diff) === 1 && isset($diff[$updatedAtColumnName])) {
            return $defaultChannels;
        }

        $diffWithoutUpdatedAtAndOrderId = array_filter($diff, function ($key) use ($updatedAtColumnName) {
            return $key !== $updatedAtColumnName && $key !== 'order_id';
        }, ARRAY_FILTER_USE_KEY);

        // Only broadcast with real changes (order_id doesn't count)
        if (count($diff) > 1 && count($diffWithoutUpdatedAtAndOrderId) === 0) {
            return [];
        }

        return $defaultChannels;
    }

    public function withRelations(): ?self
    {
        return $this->with(
            'comments',
            'comments.user',
            'comments.likes',
            'mediaSubscriptions.medium',
            'likes',
        )->find($this->id);
    }

    /**
     * Prepare a date for array / JSON serialization.
     */
    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function path(): string
    {
        return route('kanbans.show', $this->id);
    }

    public function kanban(): BelongsTo
    {
        return $this->belongsTo(Kanban::class);
    }

    public function comments(): HasMany|self
    {
        return $this->hasMany(KanbanItemComment::class);
    }

    public function subscribable(): MorphTo
    {
        return $this->morphTo();
    }

    public function subscriptions(): HasMany|self
    {
        return $this->hasMany(KanbanItemSubscription::class);
    }

    public function userSubscriptions(): HasMany|self
    {
        return $this->hasMany(KanbanItemSubscription::class)
            ->where('subscribable_type', 'App\User');
    }

    public function groupSubscriptions(): HasMany|self
    {
        return $this->hasMany(KanbanItemSubscription::class)
            ->where('subscribable_type', 'App\Group');
    }

    public function organizationSubscriptions(): HasMany|self
    {
        return $this->hasMany(KanbanItemSubscription::class)
            ->where('subscribable_type', 'App\Organization');
    }

    public function status(): HasOne|self
    {
        return $this->hasOne(KanbanStatus::class);
    }

    public function owner(): HasOne|self
    {
        return $this->hasOne('App\User', 'id', 'owner_id');
    }

    public function mediaSubscriptions(): MorphMany
    {
        return $this->morphMany('App\MediumSubscription', 'subscribable');
    }

    public function taskSubscription(): MorphMany
    {
        return $this->morphMany('App\TaskSubscription', 'subscribable');
    }

    public function media(): HasManyThrough|self
    {
        return $this->hasManyThrough(
            'App\Medium',
            'App\MediumSubscription',
            'subscribable_id', // Foreign key on medium_subscription table...
            'id', // Foreign key on medium table...
            'id', // Local key on enabling_objectives table...
            'medium_id' // Local key on medium_subscription table...
        )->where('subscribable_type', get_class($this));
    }

    /**
     * Accessor that mimics Eloquent dynamic property.
     */
    public function getEditorsAttribute(): Collection
    {
        if (! $this->relationLoaded('editors')) {
            $layers = User::whereIn('id', $this->editors_ids)->get();

            $this->setRelation('editors', $layers);
        }

        return $this->getRelation('editors');
    }

    /**
     * Access editors relation query
     */
    public function editors(?array $select = null): User|Collection|array
    {
        if ($select === null) {
            return User::whereIn('id', $this->editors_ids);
        }

        return User::whereIn('id', $this->editors_ids)->select($select)->get();
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
     */
    public function setEditorsIdsAttribute(array|string $ids): void
    {
        $this->attributes['editors_ids'] = is_string($ids) ? $ids : implode(',', array_filter($ids)); // array filter removes empty entries.
    }

    public function isAccessible(): bool
    {
        return $this->kanban->isAccessible();
    }

    public function isEditable($user = null, $sharing_token = null): bool
    {
        return $this->kanban->isEditable($user, $sharing_token);
    }

    protected static function booted(): void
    {
        static::deleting(function (KanbanItem $item) {
            $item->mediaSubscriptions->each(function (MediumSubscription $subscription) {
                // hack to skip setting medium_id of model to null
                if (is_null($subscription->additional_data)) {
                    $subscription->additional_data = true;
                }
                // can't call delete()-function of MediumSubscription-model (in general)
                app(MediumSubscriptionController::class)->destroy($subscription);
            });
        });
    }
}

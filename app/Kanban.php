<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Kanban extends Model
{
    protected $guarded = [];

    public function path()
    {
        return route('kanbans.show', $this->id);
    }

    public function items()
    {
        return $this->hasMany('App\KanbanItem', 'kanban_id', 'id')->orderBy('order_id');
    }

    public function statuses()
    {
        return $this->hasMany('App\KanbanStatus', 'kanban_id', 'id')->orderBy('order_id');
    }

    public function subscriptions()
    {
        return $this->hasMany(KanbanSubscription::class);
    }

    public function userSubscriptions()
    {
        return $this->hasMany(KanbanSubscription::class)
            ->where('subscribable_type', 'App\User');
    }

    public function groupSubscriptions()
    {
        return $this->hasMany(KanbanSubscription::class)
            ->where('subscribable_type', 'App\Group');
    }

    public function organizationSubscriptions()
    {
        return $this->hasMany(KanbanSubscription::class)
            ->where('subscribable_type', 'App\Organization');
    }

    public function owner()
    {
        return $this->hasOne('App\User', 'id', 'owner_id');
    }

    public function getBackgroundAttribute()
    {
        if ($this->color != null && $this->color != '#F4F4F4') {
            return $this->transformHexColorToRgba($this->color);
        }

        return $this->transformHexColorToRgba('#F4F4F4');
    }

    public function isAccessible()
    {
        if (
            auth()->user()->kanbans->contains('id', $this->id) // user enrolled
            or ($this->owner_id == auth()->user()->id)            // or owner
            or is_admin() // or admin
        ) {
            return true;
        } else {
            return false;
        }
    }

    public function isEditable()
    {
        $subscribtion = optional($this->userSubscriptions()->where('subscribable_id', auth()->user()->id)->first());
        if (
            $subscribtion->editable // user enrolled
            or ($this->owner_id == auth()->user()->id)            // or owner
            or is_admin() // or admin
        ) {
            return true;
        } else {
            return false;
        }
    }

    private function transformHexColorToRgba($color, $opacity = .7)
    {
        [$r, $g, $b] = sscanf($color, '#%02x%02x%02x');

        return 'rgba('.$r.', '.$g.', '.$b.', '.$opacity.')';
    }
}

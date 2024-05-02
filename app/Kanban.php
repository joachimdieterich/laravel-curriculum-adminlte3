<?php

namespace App;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

/**
 *   @OA\Schema(
 *      required={"id", "title"},
 *      @OA\Xml(name="Grade"),
 *
 *      @OA\Property( property="id", type="integer"),
 *      @OA\Property( property="title", type="string"),
 *      @OA\Property( property="description", type="string"),
 *      @OA\Property( property="color", type="string"),
 *      @OA\Property( property="medium_id", type="integer"),
 *      @OA\Property( property="owner_id", type="integer"),
 *      @OA\Property( property="created_at", type="string"),
 *      @OA\Property( property="updated_at", type="string"),
 *      @OA\Property( property="commentable", type="integer"),
 *      @OA\Property( property="auto_refresh", type="integer"),
 *      @OA\Property( property="only_edit_owned_items", type="integer")
 *   ),
 */
class Kanban extends Model
{
    protected $guarded = [];

    /* protected $dates = [  --> change v.10
         'updated_at',
         'created_at',
     ];*/

    protected $casts = [
        'commentable' => 'boolean',
        'auto_refresh' => 'boolean',
        'only_edit_owned_items' => 'boolean',
        'allow_copy' => 'boolean',
        'updated_at' => 'datetime',
        'created_at'  => 'datetime',
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

    public function medium()
    {
        return $this->hasOne('App\Medium', 'id', 'medium_id');
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
            or ($this->subscriptions->where('subscribable_type', "App\Group")->whereIn('subscribable_id', auth()->user()->groups->pluck('id')))->isNotEmpty() //user is enroled in group
            or ($this->subscriptions->where('subscribable_type', "App\Organization")->whereIn('subscribable_id', auth()->user()->current_organization_id))->isNotEmpty() //user is enroled in group
            or ($this->owner_id == auth()->user()->id)            // or owner
            or ((env('GUEST_USER') != null) ? User::find(env('GUEST_USER'))->kanbans->contains('id', $this->id) : false) //or allowed via guest
            or is_admin() // or admin
        ) {
            return true;
        } else {
            return false;
        }
    }

    public function isEditable($user_id = null, $token = null)
    {
        if ($user_id == null)
        {
            $user_id = auth()->user()->id;
        }

        if ($token == null){
            $userSubscription = optional($this->userSubscriptions()
                ->where('subscribable_id', $user_id)
                ->first());
            $groupSubscription = optional($this->groupSubscriptions()
                ->whereIn('subscribable_id', auth()->user()->groups->pluck('id'))
                ->where('editable', 1)
                ->first());
            $organizationSubscription = optional($this->organizationSubscriptions()
                ->whereIn('subscribable_id', auth()->user()->organizations->pluck('id'))
                ->where('editable', 1)
                ->first());
        }
        else
        {
            $userSubscription = optional($this->userSubscriptions()
                /*->where('subscribable_id', $user_id)*/ // fix 500 error on authenticated users
                ->where('sharing_token', $token)
                ->first());
        }
        if (
            $userSubscription->editable // user enrolled
            or $groupSubscription->editable ?? false // group enrolled
            or $organizationSubscription->editable ?? false // organization enrolled
            or ($this->owner_id == $user_id)            // or owner
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

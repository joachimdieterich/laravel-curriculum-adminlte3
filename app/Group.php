<?php

namespace App;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 *   @OA\Schema(
 *      required={"id", "title", "grade_id", "period_id", "organization_id"},
 *      @OA\Xml(name="Group"),
 *
 *      @OA\Property( property="id", type="integer"),
 *      @OA\Property( property="title", type="string"),
 *      @OA\Property( property="grade_id", type="integer"),
 *      @OA\Property( property="period_id", type="integer"),
 *      @OA\Property( property="organization_id", type="integer"),
 *      @OA\Property( property="created_at", type="string"),
 *      @OA\Property( property="updated_at", type="string")
 *   ),
 */
class Group extends Model
{
    use HasFactory;
    protected $guarded = [];

    /**
     * Prepare a date for array / JSON serialization.
     *
     * @param  \DateTimeInterface  $date
     * @return string
     */

    protected $dates = [
        'updated_at',
        'created_at',
    ];
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function path()
    {
        return "/groups/{$this->id}";
    }

    public function enrol(User $user)
    {
        return $this->users()->attach($user);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'group_user')->withTimestamps();
    }

    public function curricula()
    {
        return $this->belongsToMany(Curriculum::class, 'curriculum_group')->withTimestamps();
    }

    public function grade()
    {
        return $this->hasOne('App\Grade', 'id', 'grade_id');
    }

    public function glossar()
    {
        return $this->hasOne('App\Glossar', 'subscribable_id', 'id')->where('subscribable_type', get_class($this));
    }

    public function period()
    {
        return $this->hasOne('App\Period', 'id', 'period_id');
    }

    public function organization()
    {
        return $this->hasOne('App\Organization', 'id', 'organization_id');
    }

    public function courses()
    {
        return $this->hasMany('App\Course', 'group_id', 'id');
    }

    public function kanbans()
    {
        return $this->hasManyThrough(
            'App\Kanban',
            'App\KanbanSubscription',
            'subscribable_id',
            'id',
            'id',
            'kanban_id'
        )->where('subscribable_type', get_class($this));
    }

    public function lmsReferences()
    {
        return $this->hasManyThrough(
            'App\LmsReference',
            'App\LmsReferenceSubscription',
            'subscribable_id',
            'id',
            'id',
            'lms_reference_id'
        )->where('subscribable_type', get_class($this));
    }

    public function plans()
    {
        return $this->hasManyThrough(
            'App\Plan',
            'App\PlanSubscription',
            'subscribable_id',
            'id',
            'id',
            'plan_id'
        )->where('subscribable_type', get_class($this));
    }

    public function logbookSubscription()
    {
        return $this->morphMany('App\LogbookSubscription', 'subscribable');
    }

    public function logbooks()
    {
        return $this->hasManyThrough(
            'App\Logbook',
            'App\LogbookSubscription',
            'subscribable_id',
            'id',
            'id',
            'logbook_id'
        )->where('subscribable_type', get_class($this));
    }

    public function isAccessible()
    {
        if (
            auth()->user()->groups->contains($this)
            or ($this->organization_id == auth()->user()->current_organization_id)
            or is_admin()
        ) {
            return true;
        } else {
            return false;
        }
    }
}

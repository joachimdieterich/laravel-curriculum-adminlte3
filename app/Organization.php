<?php

namespace App;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 *   @OA\Schema(
 *      required={"id", "title", "state_id", "country_id", "organization_type_id", "status_id"},
 *      @OA\Xml(name="Organization"),
 *
 *      @OA\Property( property="id", type="integer"),
 *      @OA\Property( property="common_name", type="string"),
 *      @OA\Property( property="title", type="string"),
 *      @OA\Property( property="organization_type_id", type="string"),
 *      @OA\Property( property="state_id", type="string"),
 *      @OA\Property( property="country_id", type="string"),
 *      @OA\Property( property="status_id", type="integer"),
 *
 *      @OA\Property( property="description", type="string"),
 *      @OA\Property( property="street", type="string"),
 *      @OA\Property( property="city", type="string"),
 *      @OA\Property( property="postcode", type="string"),
 *      @OA\Property( property="phone", type="string"),
 *      @OA\Property( property="email", type="string"),
 *      @OA\Property( property="created_at", type="string"),
 *      @OA\Property( property="updated_at", type="string")
 *   ),
 */
class Organization extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $dates = [
        'updated_at',
        'created_at',
    ];

    protected $attributes = [
        'state_id' => 'DE-RP',
        'country_id' => 'DE',
        'organization_type_id' => 1,
        'status_id' => 1,
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
        return "/organizations/{$this->id}";
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'organization_role_users')
                ->withPivot(['user_id', 'role_id', 'organization_id']);
    }

    // and for all 3 Organization, User, Role this relation:
    public function organizationRolesUsers()
    {
        return $this->hasMany(OrganizationRoleUser::class)
              ->role('Admin');
    }

    public function users()
    {
        return $this->belongsToMany('App\User', 'organization_role_users')
            ->select('users.*')
            ->withPivot(['user_id', 'role_id', 'organization_id']);
    }

    public function state()
    {
        return $this->hasOne('App\State', 'code', 'state_id');
    }

    public function country()
    {
        return $this->hasOne('App\Country', 'alpha2', 'country_id');
    }

    public function type()
    {
        return $this->hasOne('App\OrganizationType', 'id', 'organization_type_id');
    }

    public function periods()
    {
        return $this->belongsToMany('App\Period', 'groups')
            ->select('periods.*')
            ->withPivot(['period_id', 'organization_id']);
    }

    public function status()
    {
        return $this->hasOne('App\StatusDefinition', 'status_definition_id', 'status_id');
    }

    public function navigators()
    {
        return $this->hasMany('App\Navigator');
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

    public function groups()
    {
        return $this->hasMany('App\Group');
    }
}

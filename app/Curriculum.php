<?php

namespace App;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 *   @OA\Schema(
 *      required={"id", "title", "description", "author", "publisher", "city", "date", "color", "grade_id", "subject_id", "organisation_type_id", "state_id", "country_id", "medium_id", "owner_id", "created_at", "updated_at"},
 *      @OA\Xml(name="Curriculum"),
 *
 *      @OA\Property( property="id", type="integer"),
 *      @OA\Property( property="description", type="string"),
 *      @OA\Property( property="author", type="string"),
 *      @OA\Property( property="publisher", type="string"),
 *      @OA\Property( property="city", type="string"),
 *      @OA\Property( property="date", type="string"),
 *      @OA\Property( property="color", type="string"),
 *      @OA\Property( property="grade_id", type="integer"),
 *      @OA\Property( property="subject_id", type="string"),
 *      @OA\Property( property="organisation_type_id", type="string"),
 *      @OA\Property( property="state_id", type="string"),
 *      @OA\Property( property="country_id", type="string"),
 *      @OA\Property( property="medium_id", type="string"),
 *      @OA\Property( property="owner_id", type="string"),
 *      @OA\Property( property="created_at", type="string"),
 *      @OA\Property( property="updated_at", type="string")
 *   ),
 */
class Curriculum extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'objective_type_order' => 'array',
        'variants' => 'array',
    ];

    protected $dates = [
        'updated_at',
        'created_at',
    ];

    protected $attributes = [
        'state_id' => 'DE-RP',
        'country_id' => 'DE',
        'color' => '#27AE60',
        'grade_id' => 1,
        'subject_id' => 51, //Math
        'organization_type_id' => 1,
        'type_id' => 4,  //= user
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
        return "/curricula/{$this->id}";
    }

    public function enrol(Group $group)
    {
        return $this->groups()->attach($group);
    }

    public function expel(Group $group)
    {
        return $this->groups()->detach($group);
    }

    public function state()
    {
        return $this->hasOne('App\State', 'code', 'state_id')
                    ->withDefault(function () {
                        return new State();
                    });
    }

    public function country()
    {
        return $this->hasOne('App\Country', 'alpha2', 'country_id');
    }

    public function grade()
    {
        return $this->hasOne('App\Grade', 'id', 'grade_id');
    }

    public function organizationType()
    {
        return $this->hasOne('App\OrganizationType', 'id', 'organization_type_id');
    }

    public function subject()
    {
        //return $this->hasOne('App\Subject', 'external_id', 'subject_id');
        return $this->hasOne('App\Subject', 'id', 'subject_id');
    }

    public function owner()
    {
        return $this->hasOne('App\User', 'id', 'owner_id');
    }

    public function groups()
    {
        return $this->belongsToMany('App\Group', 'curriculum_group')->withTimestamps();
    }

    public function enablingObjectives()
    {
        return $this->hasMany('App\EnablingObjective', 'curriculum_id', 'id')->orderBy('order_id');
    }

    public function terminalObjectives()
    {
        return $this->hasMany('App\TerminalObjective', 'curriculum_id', 'id')->orderBy('objective_type_id')->orderBy('order_id');
    }

    public function type()
    {
        return $this->hasOne('App\CurriculumType', 'id', 'type_id');
    }

    public function contentSubscriptions()
    {
        return $this->morphMany('App\ContentSubscription', 'subscribable')->orderBy('order_id');
    }

    public function contents()
    {
        return $this->hasManyThrough(
            'App\Content',
            'App\ContentSubscription',
            'subscribable_id', // Foreign key on content_subscription table...
            'id', // Foreign key on content table...
            'id', // Local key on curriculum table...
            'content_id' // Local key on content_subscription table...
        )->where('subscribable_type', get_class($this));
    }

    public function certificates()
    {
        return $this->hasMany('App\Certificate', 'curriculum_id', 'id');
    }

    public function courses()
    {
        return $this->hasMany('App\Course', 'curriculum_id', 'id');
    }

    public function mediaSubscriptions()
    {
        return $this->morphMany('App\MediumSubscription', 'subscribable');
    }

    public function medium()
    {
        return $this->hasOne('App\Medium', 'id', 'medium_id');
    }

    public function media()
    {
        return $this->hasManyThrough(
            'App\Medium',
            'App\MediumSubscription',
            'subscribable_id', // Foreign key on medium_subscription table...
            'id', // Foreign key on medium table...
            'id', // Local key on curriculum table...
            'medium_id' // Local key on medium_subscription table...
        )->where('subscribable_type', get_class($this));
    }

    public function glossar()
    {
        return $this->hasOne('App\Glossar', 'subscribable_id', 'id')->where('subscribable_type', get_class($this));
    }

    public function navigator_item()
    {
        return $this->morphOne('App\NavigatorItem', 'referenceable');
    }

    public function predecessors()
    {
        return $this->morphMany('App\Prerequisites', 'successor');
    }

    public function successors()
    {
        return $this->morphMany('App\Prerequisites', 'predecessor');
    }

    public function isAccessible()
    {
        if (
            auth()->user()->curricula()->contains('id', $this->id) // user enrolled
            or ($this->owner_id == auth()->user()->id)            // or owner
            or ((env('GUEST_USER') != null) ? User::find(env('GUEST_USER'))->curricula()->contains('id', $this->id) : false) //or allowed via guest
            or is_admin() // or admin
        ) {
            return true;
        } else {
            return false;
        }
    }
}

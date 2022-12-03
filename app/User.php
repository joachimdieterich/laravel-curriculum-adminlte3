<?php

namespace App;

use App\Domains\Exams\Models\Exam;
use App\Scopes\NoSharingUsers;
use Carbon\Carbon;
use Cmgmyr\Messenger\Models\Thread;
use Cmgmyr\Messenger\Traits\Messagable;
use DateTimeInterface;
use Hash;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 *   @OA\Schema(
 *      required={"id", "username", "firstname", "lastname", "email", "password"},
 *      @OA\Xml(name="User"),
 *
 *      @OA\Property( property="id", type="integer"),
 *      @OA\Property( property="common_name", type="string"),
 *      @OA\Property( property="username", type="string"),
 *      @OA\Property( property="firstname", type="string"),
 *      @OA\Property( property="lastname", type="string"),
 *      @OA\Property( property="email", type="string"),
 *      @OA\Property( property="email_verified_at", type="string"),
 *      @OA\Property( property="password", type="string"),
 *      @OA\Property( property="remember_token", type="integer"),
 *      @OA\Property( property="created_at", type="string"),
 *      @OA\Property( property="updated_at", type="string"),
 *      @OA\Property( property="current_organization_id", type="integer"),
 *      @OA\Property( property="current_period_id", type="integer")
 *   ),
 */
class User extends Authenticatable
{
    use HasApiTokens, SoftDeletes, Notifiable, Messagable, HasFactory;

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $dates = [
        'updated_at',
        'created_at',
        'deleted_at',
        'email_verified_at',
    ];

    protected $fillable = [
        'username',
        'common_name',
        'firstname',
        'lastname',
        'email',
        'password',
        'created_at',
        'updated_at',
        'deleted_at',
        'remember_token',
        'email_verified_at',
        'status_id',
        'organization_id',
        'current_organization_id',
        'current_period_id',
    ];

    protected static function booted()
    {
        //static::addGlobalScope(new NoSharingUsers());
    }


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
        return "/users/{$this->id}";
    }

    public function fullName()
    {
        return "{$this->firstname} {$this->lastname}";
    }

    public function comments()
    {
        return $this->hasMany(KanbanItemComment::class);
    }

    public function absences()
    {
        return $this->hasMany('App\Absence', 'absent_user_id');
    }

    public function achievements()
    {
        return $this->hasMany('App\Achievement');
    }

    public function achievements_today()
    {
        return $this->hasMany('App\Achievement')->whereDate('updated_at', Carbon::today())->get();
    }

    public function artefacts()
    {
        return $this->hasMany('App\Artefact');
    }

    public function certificates()
    {
        return $this->hasMany('App\Certificate', 'owner_id');
    }

    public function contactDetail()
    {
        return $this->hasOne('App\ContactDetail', 'owner_id');
    }

    public function contentSubscriptions()
    {
        return $this->hasMany('App\ContentSubscription', 'owner_id');
    }

    public function ownsCurricula()
    {
        return $this->hasMany('App\Curriculum', 'owner_id');
    }

    public function getEmailVerifiedAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('panel.date_format').' '.config('panel.time_format')) : null;
    }

    public function setEmailVerifiedAtAttribute($value)
    {
        $this->attributes['email_verified_at'] = $value ? Carbon::createFromFormat(config('panel.date_format').' '.config('panel.time_format'), $value)->format('Y-m-d H:i:s') : null;
    }

    public function setPasswordAttribute($input)
    {
        if ($input) {
            $this->attributes['password'] = app('hash')->needsRehash($input) ? Hash::make($input) : $input;
        }
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    public function contents()
    {
        return $this->hasMany(Content::class, 'owner_id')->latest('updated_at');
    }

    public function currentGroups()
    {
        return $this->belongsToMany('App\Group', 'group_user')
                    ->where('period_id', $this->current_period_id)
                    ->where('organization_id', $this->current_organization_id)
                    ->withTimestamps();
    }

    public function groups()
    {
        return $this->belongsToMany('App\Group', 'group_user')->withTimestamps();
    }

    public function groupsWithCurriculum($curriculum_id) //todo: used? -> better groups()->with('curricula')
    {
        return DB::table('groups')
            ->join('group_user', 'groups.id', '=', 'group_user.group_id')
            ->join('curriculum_group', 'curriculum_group.group_id', '=', 'group_user.group_id')
            ->where('group_user.user_id', $this->id)
            ->where('curriculum_group.curriculum_id', $curriculum_id)
            ->get();
    }

    public function curricula($select = ['curricula.*', 'curriculum_group.id AS course_id', 'curriculum_group.group_id AS group_id'])
    {
        return DB::table('curricula')
            ->distinct()
            ->select($select)
            ->leftjoin('curriculum_group', 'curricula.id', '=', 'curriculum_group.curriculum_id')
            ->leftjoin('group_user', 'group_user.group_id', '=', 'curriculum_group.group_id')
            ->where('group_user.user_id', $this->id)
            ->orWhere('curricula.owner_id', $this->id) //user should also see curricula which he/she owns
            ->get();
    }

    public function currentCurriculaEnrolments()
    {
        return DB::table('curricula')
           // ->distinct()
            ->select('curricula.id', 'curricula.title', 'curriculum_group.id AS course_id', 'curriculum_group.group_id AS group_id')
            ->leftjoin('curriculum_group', 'curricula.id', '=', 'curriculum_group.curriculum_id')
            ->leftjoin('group_user', 'group_user.group_id', '=', 'curriculum_group.group_id')
            ->join('groups', 'groups.id', '=', 'group_user.group_id')
            ->where('groups.period_id', $this->current_period_id)
            ->where('groups.organization_id', $this->current_organization_id)
            ->where('group_user.user_id', $this->id)
            ->orderBy('group_id')
            ->get();
    }

    public function currentGroupEnrolments()
    {
        return $this->belongsToMany('App\Group', 'group_user')
            ->select('groups.*', 'curriculum_group.id AS course_id', 'curriculum_group.curriculum_id')
            ->leftjoin('curriculum_group', 'curriculum_group.group_id', '=', 'groups.id')
            ->where('period_id', $this->current_period_id)
            ->where('organization_id', $this->current_organization_id)
            ->orderBy('groups.id')
            ->withTimestamps();
    }

    public function kanbanSubscription()
    {
        return $this->morphMany('App\KanbanSubscription', 'subscribable');
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

    public function logbookSubscription()
    {
        return $this->morphMany('App\LogbookSubscription', 'subscribable');
    }

    public function logbooks()
    {
        return $this->hasManyThrough(
            'App\Logbook',
            'App\LogbookSubscription',
            'subscribable_id', // Foreign key on logbook_subscription table...
            'id', // Foreign key on logbook table...
            'id', // Local key on logbook table...
            'logbook_id' // Local key on logbook_subscription table...
        )->where('subscribable_type', get_class($this));
    }

    public function media()
    {
        return $this->hasMany('App\Medium', 'owner_id');
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

    public function periods()
    {
        return DB::table('periods')
            ->select('periods.*', 'groups.organization_id AS organization_id')
            ->join('groups', 'groups.period_id', '=', 'periods.id')
            ->join('group_user', 'group_user.group_id', '=', 'groups.id')
            ->where('group_user.user_id', $this->id)
            ->distinct()
            ->get();
    }

    public function currentPeriods()
    {
        return DB::table('periods')
            ->select('periods.*', 'groups.organization_id AS organization_id')
            ->join('groups', 'groups.period_id', '=', 'periods.id')
            ->join('group_user', 'group_user.group_id', '=', 'groups.id')
            ->where('group_user.user_id', $this->id)
            ->where('groups.organization_id', $this->current_organization_id)
            ->distinct()
            ->get();
    }

    public function ownsPrerequisites()
    {
        return $this->hasMany('App\Prerequisites', 'owner_id');
    }

    public function progresses()
    {
        return $this->morphMany('App\Progress', 'associable');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'organization_role_users')
                ->withPivot(['user_id', 'role_id', 'organization_id']);
    }

    /**
     * current role, based on current_organization_id organization_role_users
     */
    public function role()
    {
        return $this->belongsToMany(Role::class, 'organization_role_users')
            ->withPivot(['user_id', 'role_id', 'organization_id'])
            ->where('organization_role_users.organization_id', $this->current_organization_id)->first();
    }

    public function unreadMessagesCount()
    {
        return Thread::userUnreadMessagesCount($this->id);
    }

    /**
     * permissions of the current role
     */
    public function permissions()
    {
        return DB::table('permissions')
            ->join('permission_role', 'permission_role.permission_id', '=', 'permissions.id')
            ->join('organization_role_users', 'organization_role_users.role_id', '=', 'permission_role.role_id')
            ->where('organization_role_users.organization_id', $this->current_organization_id)
            ->where('organization_role_users.user_id', $this->id)
            ->get();
    }

    public function tasks()
    {
        return $this->hasManyThrough(
            'App\Task',
            'App\TaskSubscription',
            'subscribable_id',
            'id',
            'id',
            'task_id'
        )->where('subscribable_type', get_class($this));
    }

    public function organizations()
    {
        return $this->belongsToMany(Organization::class, 'organization_role_users')
            ->withPivot(['user_id', 'role_id', 'organization_id']);
    }

    /**
     * @return OrganizationRoleUser
     */
    public function organizationRolesUsers()
    {
        return $this->hasMany(OrganizationRoleUser::class);
    }

    public function status()
    {
        return $this->hasOne('App\StatusDefinition', 'status_definition_id', 'status_id');
    }

    public function currentRole()
    {
        return $this->roles()
                ->where('user_id', '=', $this->id)
                ->where('organization_id', $this->current_organization_id)
                ->get();
    }

    public function users()
    {
        return (auth()->user()->role()->id == 1) ? User::select('id', 'username', 'firstname', 'lastname') : Organization::where('id', auth()->user()->current_organization_id)->get()->first()->users()->select('id', 'username', 'firstname', 'lastname', 'deleted_at'); //todo, get all users of all organizations not only current
    }

    public function getAvatarAttribute()
    {
        return ($this->medium_id !== null) ? '/media/'.$this->medium_id : (new \Laravolt\Avatar\Avatar)->create($this->fullName())->toBase64()->encoded;
    }

    public function mayAccessUser(User $user, $context = 'organization')
    {
        switch ($context) {
            case 'organization': return $user->organizations->pluck('id')->contains($this->current_organization_id);
            //Todo: check for groups
            break;
            default: return false;
        }
    }

    public function scopeNoSharing($query){
        $query->whereNull('sharing_token');
    }

    public function exams()
    {
        return $this->belongsToMany(
            Exam::class,
            'exam_user',
            'user_id',
            'exam_id')
            ->withPivot(['login_data', 'exam_completed_at']);
    }

}

<?php

namespace App;

use Carbon\Carbon;
use Hash;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\DB;
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
 * 
 */
class User extends Authenticatable
{
    use HasApiTokens, SoftDeletes, Notifiable;

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
    
    public function path()
    {
        return "/admin/users/{$this->id}";
    }

    public function fullName()
    {
        return "{$this->firstname} {$this->lastname}";
    }
    
    public function getEmailVerifiedAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('panel.date_format') . ' ' . config('panel.time_format')) : null;
    }

    public function setEmailVerifiedAtAttribute($value)
    {
        $this->attributes['email_verified_at'] = $value ? Carbon::createFromFormat(config('panel.date_format') . ' ' . config('panel.time_format'), $value)->format('Y-m-d H:i:s') : null;
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
    
    public function groups()
    {
        return $this->belongsToMany('App\Group', 'group_user')->withTimestamps();
    } 
    
    public function curricula()
    {

        return DB::table('curricula')
            ->join('curriculum_group', 'curricula.id', '=', 'curriculum_group.curriculum_id')
            ->join('group_user', 'group_user.group_id', '=', 'curriculum_group.group_id')
            ->where('group_user.user_id', auth()->user()->id)
            ->get();
    }
    
    public function roles()
    {
         return $this->belongsToMany(Role::class, 'organization_role_users')
                ->withPivot(['user_id', 'role_id', 'organization_id']);
    }
    
    public function organizations()
    {
        return $this->belongsToMany(Organization::class, 'organization_role_users')
            ->withPivot(['user_id', 'role_id', 'organization_id']);
    }
    
    /**
     * 
     * @return OrganizationRoleUser
     */ 
    public function organizationRolesUsers()
    {
      return $this->hasMany(OrganizationRoleUser::class);
    }
    
    public function status()
    {
        return $this->hasOne('App\Status', 'status_id', 'status_id');
    }
    
    
    public function currentRole()
    { 
        return $this->roles()
                ->where('user_id', '=', $this->id)
                ->where('organization_id', $this->current_organization_id)
                ->get();
       
    }
}

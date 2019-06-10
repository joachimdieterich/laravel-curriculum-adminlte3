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

class User extends Authenticatable
{
    use SoftDeletes, Notifiable, HasApiTokens;

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
        'organization_id'
    ];

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

    /**
     * Enrol to institution with given role
     * @param int $institution_id
     * @param int $role_id
     * @return OrganizationRoleUser
     */
    public function enrol($dependency, $user_id, $reference_id, $role_id = null)
    {
        switch ($dependency) {
            case 'organization':    return OrganizationRoleUser::firstOrCreate([
                                        'user_id'         => $user_id,
                                        'organization_id' => $reference_id,
                                        'role_id'         => $role_id,
                                    ]);  
                break;
            case 'group':           $user = User::find($user_id);
                                    User::findOrFail($user_id)->enrol('organization', $user_id, $reference_id, 6); //if not enroled enrol as student
                                    return $user->groups()->syncWithoutDetaching([
                                        'group_id' => $reference_id
                                    ]);
                                   
                break;

            default:
                break;
        }
              
    }
    
    public function expel($dependency, $user_id, $reference_id)
    {
        switch ($dependency) {
            case 'organization':    return OrganizationRoleUser::where([
                                        'user_id' => $user_id,
                                        'organization_id' => $reference_id,
                                    ])->delete();  
                break;
            case 'group':           $user = User::find($user_id);
                                    return $user->groups()->detach([
                                        'group_id' => $reference_id
                                    ]);
                                   
                break;

            default:
                break;
        }
        
    }
    
    public function contents()
    {
        return $this->hasMany(Content::class, 'owner_id')->latest('updated_at');
    }
    
    public function groups()
    {
        return $this->belongsToMany('App\Group', 'group_user');
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
}

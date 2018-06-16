<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use \App\Traits\RandomId;

class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;
    use RandomId;

    protected $dates = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 
        'email',
        'password',
        'about',
        'language',
        'location', 
        'facebook_profile_url', 
        'twitter_profile_url',
        'linkedin_profile_url',
        'yammer_profile_url',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public static function validationRules($except = null)
    {
        $rules = [
            'name' => 'required',
            'email' => 'required|unique:users,email', # the id has to be added in controller
            'roles' => 'exists:roles,id',
            'facebook_profile_url' => 'nullable|url', 
            'twitter_profile_url' => 'nullable|url',
            'linkedin_profile_url' => 'nullable|url',
            'yammer_profile_url' => 'nullable|url',
        ];

        if($except) {
            $rules = array_except($rules, $except);
        }

        return $rules;
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function($user) {
            $user->generateUniqueId();
        });

        static::deleting(function($user)
        {
            if ($user->isForceDeleting())
            {
                $user->roles()->detach();
            }
        });
    }

    public function __toString()
    {
        return sprintf('User "%s"', $this->name);
    }
    
    public function roles()
    {
        return $this->belongsToMany(\App\Role::class);
    }

    public function hasRole($role_name)
    {
        $role = $this->roles->where('name', $role_name);
        return (count($role) > 0);
    }

    public function newAvatarFileName()
    {
        return $this->newUuid() . '.jpg';
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use \App\Traits\RandomId;

class Circle extends Model
{
    use RandomId;

    protected $fillable = [
        'type', 
        'title',
        'limit',
        'description',
        'begin'
    ];

    protected $casts = [
        'completed' => 'boolean',
    ];

    public static function validationRules($except = null)
    {
        $rules = [
            'type' => 'required|in:'.implode(',', config('circle.defaults.types')),
            'begin' => 'required|date'
        ];

        if($except)
        {
            $rules = array_except($rules, $except);
        }

        return $rules;
    }

    public function __toString()
    {
        return sprintf('Circle %d', $this->id);
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function($circle) {
            $circle->generateUniqueId();
        });

        static::deleting(function($circle) {
            $circle->languages()->detach();

            $circle->memberships()->delete();
        });
    }

    public function languages()
    {
        return $this->belongsToMany(\App\Language::class);
    }

    public function memberships()
    {
        return $this->hasMany(\App\Membership::class);
    }

    public function membershipOf($user)
    {
        return \App\Membership::where(['circle_id' => $this->id, 'user_id' => $user->id])->first();
    }

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }

    public function users()
    {
        return $this->hasManyThrough('App\User', 'App\Membership', 'circle_id', 'id', 'id', 'user_id');
    }

    public function deletable()
    {
        if($this->memberships()->count() > 0)
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    public function full()
    {
        if($this->memberships()->count() >= $this->limit)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function joinable($user = null)
    {
        if($user && $this->joined($user))
        {
            return false;
        }

        return !$this->full() && !$this->completed;
    }

    public function joined($user)
    {
        if(\App\Membership::where(['circle_id' => $this->id, 'user_id' => $user->id])->count() > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function join($membership_data, $user)
    {
        if($this->joinable($user) == false)
        {
            return null;
        }

        $membership = new \App\Membership;
        $membership->fill($membership_data);
        $membership->user_id = $user->id;
        $membership->circle_id = $this->id;
        $membership->save();

        if($this->memberships()->count() >= $this->limit)
        {
            $this->complete();
        }

        return $membership;
    }

    public function joinWithDefaults($user)
    {
        $default_membership_data = [
            'type' => $this->type,
            'begin' => $this->begin
        ];

        return $this->join($default_membership_data, $user);
    }

    public function leave($user)
    {
        $memberships = \App\Membership::where(['circle_id' => $this->id, 'user_id' => $user->id]);
        
        if($memberships->count() > 0)
        {
            $memberships->first()->delete();
        }
    }

    public function ownedBy($user)
    {
        return $this->user->id == $user->id;
    }

    public function complete()
    {
        $this->completed = true;
        $this->save();
    }

    public function uncomplete()
    {
        $this->completed = false;
        $this->save();
    }
}

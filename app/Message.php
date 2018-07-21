<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use \App\Traits\RandomId;
use Carbon\Carbon;

class Message extends Model
{
    use SoftDeletes;
    use RandomId;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'body',
        'show_to_all'
    ];

    protected $casts = [
        'recipients' => 'array',
        'show_to_all' => 'boolean'
    ];

    public static function validationRules($except = null)
    {
        $rules = [
            'body' => 'required',
            'show_to_all' => 'boolean',
        ];

        if ($except) {
            $rules = array_except($rules, $except);
        }

        return $rules;
    }
    
    protected static function boot()
    {
        parent::boot();

        static::created(function ($user) {
            $user->generateUniqueId();
        });
    }

    public function circle()
    {
        return $this->belongsTo(\App\Circle::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }

    public function visibleBy($user)
    {
        if ($this->show_to_all) {
            return true;
        }

        if (in_array($user->id, $this->recipients)) {
            return true;
        } else {
            return false;
        }
    }
}

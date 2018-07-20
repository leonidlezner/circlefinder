<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TimeSlot extends Model
{
    private $current_user = null;
    private $time_offset = 0;

    public function membership()
    {
        return $this->belongsTo(\App\Membership::class);
    }

    protected $fillable = [
        'time',
        'monday',
        'tuesday',
        'wednesday',
        'thursday',
        'friday',
        'saturday',
        'sunday',
    ];

    protected $casts = [
        'monday' => 'array',
        'tuesday' => 'array',
        'wednesday' => 'array',
        'thursday' => 'array',
        'friday' => 'array',
        'saturday' => 'array',
        'sunday' => 'array',
    ];

    public static function validationRules($except = null)
    {
        $rules = [
            #'time' => 'required|in:' . implode(',', config('circle.defaults.types')),
        ];

        if ($except) {
            $rules = array_except($rules, $except);
        }

        return $rules;
    }

    public function atTime($time)
    {
        foreach (\App\TimeTable::getDayList() as $day) {
            if (is_array($this->$day) && in_array($time, $this->$day)) {
                return true;
            }
        }

        return false;
    }

    public function setTimeOffset($offset)
    {
        $this->time_offset = $offset;
    }

    private function setAsUtc($attribute_name, $value)
    {
        if (is_array($value)) {
            $value = array_map(function ($t) {
                return $t - $this->time_offset;
            }, $value);
    
            $this->attributes[$attribute_name] = json_encode($value);
        } else {
            $this->attributes[$attribute_name] = 0;
        }
    }

    private function getFromUtc($attribute_name)
    {
        if (is_null($this->attributes[$attribute_name])) {
            return 0;
        }

        $value  = json_decode($this->attributes[$attribute_name]);

        if (is_array($value)) {
            $value = array_map(function ($t) {
                return $t + $this->time_offset;
            }, $value);
        } else {
            $value = 0;
        }

        return $value;
    }

    public function __set($key, $value)
    {
        if (key_exists($key, $this->casts)) {
            $this->setAsUtc($key, $value);
            return;
        }

        return parent::__set($key, $value);
    }

    public function __get($key)
    {
        if (key_exists($key, $this->casts)) {
            return $this->getFromUtc($key);
        }

        return parent::__get($key);
    }
}

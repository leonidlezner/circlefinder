<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'name', 'title',
    ];

    public function __toString()
    {
        return sprintf('Role "%s"', $this->title);
    }

    public function users()
    {
        return $this->belongsToMany(\App\User::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($role) {
            if ($role->isForceDeleting()) {
                $role->users()->detach();
            }
        });
    }
}

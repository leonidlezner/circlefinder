<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \App\Traits\RandomId;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use phpDocumentor\Reflection\Types\Self_;

class PrivateMessage extends Model
{
    use SoftDeletes;
    use RandomId;

    protected static function boot()
    {
        parent::boot();

        static::creating(function (PrivateMessage $message) {
            if (Auth::check()) {
                $message->user_id = Auth::id();
            }
        });

        static::created(function (PrivateMessage $message) {
            $message->generateUniqueId();
        });
    }

    protected $dates = [
        'read_at'
    ];

    protected $fillable = [
        'body',
        'recipient_id'
    ];

    public function recipient()
    {
        return $this->belongsTo(User::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function validationRules($except = null)
    {
        $rules = [
            'body' => 'required',
            'recipient_id' => 'required',
        ];

        if ($except) {
            $rules = array_except($rules, $except);
        }

        return $rules;
    }
}

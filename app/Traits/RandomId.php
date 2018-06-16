<?php

namespace App\Traits;
use Illuminate\Support\Str;

trait RandomId
{
    public function newUuid()
    {
        return (string) Str::orderedUuid();
    }

    public function generateUniqueId()
    {
        $this->uuid = $this->newUuid();
        $this->save();
    }

    public static function withUuid($uuid)
    {
        return self::where('uuid', $uuid);
    }
}
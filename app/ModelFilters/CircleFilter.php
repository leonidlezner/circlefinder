<?php

namespace App\ModelFilters;

use EloquentFilter\ModelFilter;

class CircleFilter extends ModelFilter
{
    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = [
        'circles' => ['languages']
    ];

    public function type($type)
    {
        return $this->where('type', $type);
    }

    public function language($language)
    {
        return $this->related('languages', 'code', '=', $language);
    }

    public function status($status)
    {
        switch ($status) {
            case 'completed':
                return $this->where([
                    'completed' => true
                ]);
            case 'full':
                return $this->where([
                    'full' => true
                ]);
            default:
                return $this->where([
                    'completed' => false,
                    'full' => false
                ]);
        }
    }
}

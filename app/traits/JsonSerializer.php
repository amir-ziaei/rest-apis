<?php

namespace App\Traits;

trait JsonSerializer {
    public function jsonSerialize()
    {
       return  array_filter(get_object_vars($this), function ($key) {
           return ! in_array($key, ['db']);
       }, ARRAY_FILTER_USE_KEY);
    }
}
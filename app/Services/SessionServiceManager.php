<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Session;

class SessionServiceManager
{
    public static function getValue($key,$default =null)
    {
			return session()->get(getSessionPrefix().$key,$default);
    }

    public static function updateValue($key, $value)
    {
			session()->forget(getSessionPrefix().$key);
			session()->put(getSessionPrefix().$key , strtoupper($value), 1440); // 1d day
			session()->save();
			return $value;
    }
}


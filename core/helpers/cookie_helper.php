<?php


namespace App\Core\Helpers;


use App\Core\App;

class cookie_helper
{
    const DEFAULT_EXP_TIME = 'LONG';

    public static function set($key, $value)
    {
        setcookie($key, $value, self::get_default_expiry_time(), '/');
    }

    public static function get($key)
    {
        if(isset($_COOKIE[$key]))
            return $_COOKIE[$key];
        return null;
    }

    public static function delete($key)
    {
        if (isset($_COOKIE[$key])) {
            unset($_COOKIE[$key]);
            setcookie($key, null, -1, '/');
            return true;
        } else {
            return false;
        }
    }

    public static function get_default_expiry_time()
    {
        return strtotime(
            App::get('config')['cookies']['expiry'][self::DEFAULT_EXP_TIME]
        );
    }

}
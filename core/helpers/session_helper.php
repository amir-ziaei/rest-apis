<?php

namespace App\Core\Helpers;


class session_helper
{
    public function __construct()
    {
        $this->start();
    }

    public function start()
    {
        session_start();
    }

    public function destroy()
    {
        session_destroy();
    }

    public function restart()
    {
        $this->destroy();
        $this->start();
    }

    public function get($key)
    {
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }
        return null;
    }

    public function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public function isset($key)
    {
        return isset($_SESSION[$key]);
    }

    public function unset($key)
    {
        unset($_SESSION[$key]);
    }
}
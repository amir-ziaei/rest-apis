<?php

function dd($data)
{
    echo "<pre>";
    die(var_dump($data));
    echo "</pre>";
}

function HTTP_RAW_POST_DATA()
{
    return file_get_contents("php://input");
}

function safe($str)
{
    return filter_var($str, FILTER_SANITIZE_STRING);
}

function random_hash($length = 16)
{
    $hash = bin2hex(random_bytes($length));
    return serialize($hash);
}

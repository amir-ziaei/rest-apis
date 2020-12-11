<?php


namespace App\Traits;


trait Responses
{
    public function e404($message = "")
    {
        return [
            "error" => "Not Found",
            "status" => 404,
            "message" => $message
        ];
    }

    public function e400($message = "")
    {
        return [
            "error" => "Bad Request",
            "status" => 400,
            "message" => $message
        ];
    }

    public function e401($message = "")
    {
        return [
            "error" => "Unauthorized",
            "status" => 401,
            "message" => $message
        ];
    }

    public function e422($message = "")
    {
        return [
            "error" => "Unprocessable Entity",
            "status" => 422,
            "message" => $message
        ];
    }

    public function e409($message = "")
    {
        return [
            "error" => "Conflict",
            "status" => 409,
            "message" => $message
        ];
    }

    public function e503($message = "")
    {
        return [
            "error" => "Service Unavailable",
            "status" => 503,
            "message" => $message
        ];
    }

    public function s201($message = "", $data = "")
    {
        return [
            "status" => 201,
            "message" => $message,
            "data" => $data
        ];
    }

    public function s200($data = "")
    {
        return [
            "status" => 200,
            "data" => $data
        ];
    }
}
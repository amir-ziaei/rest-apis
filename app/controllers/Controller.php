<?php
/**
 * Base Controller
 */

namespace App\Controller;

class Controller
{

    protected function post($property)
    {
        return @$this->validationParams[$property];
    }

    protected function validation()
    {
        $methodName = "rules_for_" . debug_backtrace()[1]['function'];
        $rules = call_user_func_array(array(get_called_class(), $methodName), []);

        $run = $this->ParsleyRun($rules);

        if( ! $run )
            return $this->e400($this->firstOfAllError);

    }

    protected function e404($message = '')
    {
        return [
            "error" => "Not Found",
            "status" => 404,
            "message" => $message
        ];
    }

    protected function e400($message = '')
    {
        return [
            "error" => "Bad Request",
            "status" => 400,
            "message" => $message
        ];
    }

    protected function e503($message = '')
    {
        return [
            "error" => "Service Unavailable",
            "status" => 503,
            "message" => $message
        ];
    }

    protected function s201($data = [])
    {
        return [
            "status" => 201,
            "data" => $data
        ];
    }

    protected function s200($data)
    {
        return [
            "status" => 200,
            "data" => $data
        ];
    }
}
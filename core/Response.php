<?php
/**
 * Response Class
 */

namespace App\Core;

class Response extends \Sabre\HTTP\Response
{
    public function send($response)
    {
        \Sabre\HTTP\Sapi::sendResponse($this->status($response["status"])
            ->header('Content-Type', 'application/json;charset=utf-8')
            ->header('Access-Control-Allow-Origin','*')
            ->header('Access-Control-Allow-Methods','*')
            ->header('Access-Control-Max-Age: 86400', '86400')
            ->body(json_encode($response))
        );
        die();
    }

    public function e404()
    {
        $this->send([
            "error" => "Not Found",
            "status" => 404,
            "message" => ""
        ]);
    }

    public function e403()
    {
        $this->send([
            "error" => "Forbidden",
            "status" => 403,
            "message" => ""
        ]);
    }


    protected function status($status)
    {
        parent::setStatus($status);
        return $this;
    }

    protected function header(string $name, $value)
    {
        parent::setHeader($name, $value);
        return $this;
    }

    protected function body($body)
    {
        parent::setBody($body);
        return $this;
    }
}
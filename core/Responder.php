<?php
/**
 * Response Class
 */

namespace App\Core;

class Responder extends \Sabre\HTTP\Response
{
    public function response($response, $allowed_methods = "*")
    {
        \Sabre\HTTP\Sapi::sendResponse($this->status($response["status"])
            ->header('Content-Type', 'application/json;charset=utf-8')
            ->header('Access-Control-Allow-Origin','*')
            ->header('Access-Control-Allow-Methods', $allowed_methods)
            ->header('Referrer-Policy','strict-origin-when-cross-origin')
            ->header('Expires', gmdate('D, d M Y H:i:s \G\M\T', time() + (60 * 60)))
            ->header('Access-Control-Max-Age: 86400', '86400')
            ->body(json_encode($response))
        );
        die();
    }

    public function e404()
    {
        $this->response([
            "error" => "Not Found",
            "status" => 404,
            "message" => ""
        ]);
    }

    public function e405($allowed_methods)
    {
        $this->response([
            "error" => "Method Not Allowed",
            "status" => 405,
            "message" => ""
        ], $allowed_methods);
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
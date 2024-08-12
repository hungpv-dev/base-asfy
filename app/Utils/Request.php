<?php

namespace App\Utils;

use Session;

class Request
{
    public function input($key = null, $default = null)
    {
        $all = $this->all();
        return htmlspecialchars($all[$key] ?? $default);
    }

    public function user(){
        return $_SESSION['authentication'] ?? NULL;
    }

    public function all()
    {
        $data = [];
        $dataGet = $_GET;
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        if (strpos($contentType, 'application/json') !== false) {
            $data = json_decode(file_get_contents('php://input'), true);
            if (!is_array($data)) {
                $data = [];
            }
        } elseif (strpos($contentType, 'multipart/form-data') !== false) {
            $data = array_merge($_POST, $_FILES);
        } else {
            $data = $_POST;
        }

        $all = array_merge($dataGet, $data);
        return $all;
    }

    public function has($key)
    {
        $allData = $this->all();
        return isset($allData[$key]);
    }
    public function query($key = null, $default = null)
    {
        if ($key === null) {
            return $_GET;
        }

        return $_GET[$key] ?? $default;
    }
    public function file($key = null)
    {
        if ($key === null) {
            return $_FILES;
        }

        return $_FILES[$key] ?? null;
    }
    public function method()
    {
        return $_SERVER['REQUEST_METHOD'];
    }
    public function fullUrl()
    {
        $isHttps = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
        $scheme = $isHttps ? 'https' : 'http';
        $url = $scheme . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        return strtok($url, '?');
    }
    public function uri()
    {
        return $_SERVER['REQUEST_URI'];
    }
    public function path()
    {
        return trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    }
    function session($flush = false)
    {
        return new Session($flush);
    }
    public function ajax()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }
    public static function getRealIPAddress()
    {
        return $_SERVER['HTTP_CLIENT_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '';
    }

    public function back(){
        return back();
    }
    public function validate($rules, $message = [], $attribute = []){
        return new Validator($this->all(),$rules,$message,$attribute);
    }

    public function response($data, $status = 200, $headers = [], $type = 'application/json') {
        
        http_response_code($status);

        foreach ($headers as $key => $value) {
            header("$key: $value");
        }

        header("Content-Type: $type");

        if ($type === 'application/json') {
            echo json_encode($data);
        } else if ($type === 'text/html') {
            echo $data;
        } else if ($type === 'text/plain') {
            echo $data;
        }

        exit;
    }
}
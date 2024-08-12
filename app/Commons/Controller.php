<?php 
namespace App\Commons;


class Controller
{
    public function sendResponse($data, $status = 200, $headers = [], $type = 'application/json') {
        
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


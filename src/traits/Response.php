<?php
namespace App\Traits;

trait Response{

    public function simpleResponse(int $repsonse_code, string $message, bool $status = true, array $params = []){
        http_response_code($repsonse_code);
        $response = array_merge(
            [
                'status' => $status,
                'message' => $message,
            ],
            $params);
        return json_encode($response);
    }

    public function objectResponse(int $response_code, $response)
    {
        http_response_code($response_code);
        return json_encode($response);
    }

    public function fileResponse(int $response_code, $file){
        http_response_code($response_code);
        return base64_encode($file);
    }
}
<?php

use App\Interfaces\DTO\ErrorInterface;
use Illuminate\Http\Response;

if (!function_exists('responseJson')) {
//    todo middleware ?
    function responseJson(mixed $result = null, ?ErrorInterface $errors = null, ?int $status = null): Response
    {
        $data = [];

        if ($result) {
            $data['result'] = $result;
        } else if ($errors) {

            $data['errors'] = [$errors];

            if (!$status) {
                $status = Response::HTTP_BAD_REQUEST;
            }
        }

        if (empty($data))
            $status = Response::HTTP_NO_CONTENT;

        return response(
            $data,
            $status ?? Response::HTTP_OK,
        );
    }
}

if (!function_exists('getNumbers')) {
    function getNumbers(string $var): string
    {
        preg_match_all('/[\d]/', $var, $matches);
        return implode('', $matches[0]);
    }
}

if (!function_exists('getIPAddress')) {
    function getIPAddress(): string
    {

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }

        return $ip ?? $_SERVER['REMOTE_ADDR'];
    }
}

if (!function_exists('secureVal')) {
    function safeVal(string $var): string
    {
        return addslashes(strip_tags(trim($var)));
    }
}
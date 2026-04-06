<?php

use Illuminate\Http\Response;

if (!function_exists('responseJson')) {
//    todo middleware ?
    function responseJson(bool $success = true, mixed $result = null, int $status = null): Response
    {
        $data = ['success' => $success, 'result' => $result];

        if (!$success) {

            $data['error'] = $result;
            unset($data['result']);

            if (!$status) {
                $status = Response::HTTP_BAD_REQUEST;
            }
        }

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
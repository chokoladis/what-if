<?php

if (!function_exists('responseJson')) {
//    todo middleware ?
    function responseJson(bool $success = true, mixed $result = null, int $status = null)
    {
        $data = ['success' => $success, 'result' => $result];

        if (!$success) {

            $data['error'] = $result;
            unset($data['result']);

            if (!$status) {
                $status = \App\Services\ResponseService::RESPONSE_BAD_REQUEST;
            }
        }

        return response(
            $data,
            $status ?? \App\Services\ResponseService::RESPONSE_OK,
        );
    }
}

if (!function_exists('getNumbers')) {
    function getNumbers($var)
    {
        preg_match_all('/[\d]/', $var, $matches);
        return implode('', $matches[0]);
    }
}

if (!function_exists('getIPAddress')) {
    function getIPAddress()
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
    function safeVal(string $var)
    {
        return addslashes(strip_tags(trim($var)));
    }
}
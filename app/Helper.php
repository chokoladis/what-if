<?php

if (!function_exists('responseJson')) {
    function responseJson(bool $success = true, array $result = null, int $status = 200)
    {
        $data = ['success' => $success,'result' => $result, 'status' => $status];

        if (!$success) {

            $data['error'] = $result;
            unset($data['result']);

            if ($data['status'] === 200){
                $data['status'] = 400;
            }
        }

        return response()
            ->json($data)
            ->setEncodingOptions(JSON_UNESCAPED_UNICODE)
            ->setStatusCode($status);
    }
}

if (!function_exists('getNumbers')){
    function getNumbers($var){
        preg_match_all('/[\d]/', $var, $matches);
        return implode('', $matches[0]);
    }
}
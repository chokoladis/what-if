<?php

namespace App\Services;

class CaptchaService
{
    const BASE_URL = 'https://api.hcaptcha.com/siteverify';

    public function verify(string $response): bool
    {
        $prepareQuery = [
            'secret' => config('services.h_captcha.secret'),
            'remoteip' => getIPAddress(),
            'response' => $response
        ];

        $url = $this->BASE_URL . '?' . http_build_query($prepareQuery);
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        $curl_response = curl_exec($curl);
        $errors = curl_error($curl);

        $result = json_decode($curl_response, 1);

        var_dump($result);
        if ($result['success']) {

            if ($result['score'] > 0.5) {
            echo 'daaa';
            } else {
//                $result['error-codes']
            }

        } else {
            var_dump($errors);
        }

        return true;
    }


}
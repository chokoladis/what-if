<?php

namespace App\Services;

use App\Models\Setting;

class CaptchaService
{
    const BASE_URL = 'https://api.hcaptcha.com/siteverify';

    public function verify(string $response): mixed
    {
        $prepareQuery = [
            'secret' => $this->getSecret(),
            'remoteip' => getIPAddress(),
            'response' => $response
        ];

        $url = self::BASE_URL . '?' . http_build_query($prepareQuery);
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        $curl_response = curl_exec($curl);
        $errors = curl_error($curl);

        $result = json_decode($curl_response, 1);

        if ($result['success']) {
            if (isset($result['score']) && $result['score'] < 0.5){
                return [false, $result['error-codes']];
            }
        } else {
            return [$result['success'], array_merge($result['error-codes'], [$errors]) ];
        }

        return [true, null];
    }

    private function getSecret()
    {
        $arResult = Setting::query()
            ->where('name', 'h_captcha_secret')
            ->first();

        if (!empty($arResult) && $arResult->value){
            return $arResult->value;
        }

        return config('services.h_captcha.secret');
    }

    public static function getSitekey()
    {
        $arResult = Setting::query()
            ->orWhere('name', 'h_captcha_sitekey')
            ->first();

        if (!empty($arResult) && $arResult->value) {
            return $arResult->value;
        }

        return config('services.h_captcha.sitekey');
    }

}
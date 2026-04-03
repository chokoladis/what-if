<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;

class CaptchaService
{
    const BASE_URL = 'https://api.hcaptcha.com/siteverify';

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

    public function verify(string $captcha): mixed
    {
        $prepareQuery = [
            'secret' => $this->getSecret(),
            'remoteip' => getIPAddress(),
            'response' => $captcha
        ];

        $response = Http::post(self::BASE_URL . '?' . http_build_query($prepareQuery));
//        todo check status/errors
        $result = json_decode($response->getBody()->getContents(), true);

        if ($result['success']) {
            if (isset($result['score']) && $result['score'] < 0.5) {
                return [false, $result['error-codes']];
            }
        } else {
            return [$result['success'], $result['error-codes']];
        }

        return [true, null];
    }

    private function getSecret()
    {
        $arResult = Setting::query()
            ->where('name', 'h_captcha_secret')
            ->first();

        if (!empty($arResult) && $arResult->value) {
            return $arResult->value;
        }

        return config('services.h_captcha.secret');
    }

}
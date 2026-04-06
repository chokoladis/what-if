<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Setting;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Throwable;

class CaptchaService
{
    const BASE_URL = 'https://api.hcaptcha.com/siteverify';

    public static function getSitekey(): string
    {
        $arResult = Setting::query()
            ->orWhere('name', 'h_captcha_sitekey')
            ->first();

        return $arResult?->value ?? config('services.h_captcha.sitekey');
    }

    /**
     * @param string $captcha
     * @return array{bool, string|null}
     * @throws ConnectionException
     */
    public function verify(string $captcha): array
    {
        $response = Http::post(self::BASE_URL . '?' . http_build_query([
                'secret' => $this->getSecret(),
                'remoteip' => getIPAddress(),
                'response' => $captcha
            ]));
//        todo check status/errors

        try {
            $response->throw();
        } catch (Throwable $th) {
            return [false, $response->json('error') ?? 'undefined'];
        }

        $result = $response->json();

        if ($result['success']) {
            if (isset($result['score']) && $result['score'] < 0.5) {
                return [false, $result['error-codes']];
            }
        } else {
            return [$result['success'], $result['error-codes']];
        }

        return [true, null];
    }

    private function getSecret(): string
    {
        $arResult = Setting::query()
            ->where('name', 'h_captcha_secret')
            ->first();

        return $arResult?->value ?? config('services.h_captcha.secret');
    }

}
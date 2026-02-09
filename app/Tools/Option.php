<?php

namespace App\Tools;

use App\Models\Setting;

class Option
{
    private static function baseQuery(string $name)
    {
        return Setting::query()->where('name', $name)->first();
    }

    static function isCaptchaSetOn()
    {
        $result = self::baseQuery('captcha_set_on');

        if (!empty($result)) {
            return filter_var($result->value, FILTER_VALIDATE_BOOLEAN);
        }

        return false;
    }

    static function isSmartCacheOn()
    {
//        todo make cache|redis or use .env
        $result = self::baseQuery('smart_cache_on');

        if (!empty($result)) {
            return filter_var($result->value, FILTER_VALIDATE_BOOLEAN);
        }

        return config('cache.smart_cache') ?? false;
    }
}
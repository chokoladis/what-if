<?php

namespace App\Services;

use Error;
use http\Cookie;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;

class SettingService {
    
    const SUPPORT_LANG = ['ru', 'en'];

    public function setLang(string $lang):mixed
    {
        if (in_array($lang, self::SUPPORT_LANG)){

            $newLang = $lang ?? config('app.locale');
            App::setLocale($newLang);
            \Illuminate\Support\Facades\Cookie::queue('lang', $newLang, 36000000);

            return [true, null];
        } else {
            return [false, new Error(__('This lang not supporting'))];
        }
    }
}

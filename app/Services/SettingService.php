<?php

namespace App\Services;

use Error;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;

class SettingService {
    
    const SUPPORT_LANG = ['ru', 'en'];

    public function setLang(Request $request,  string $lang)
    {
        if (in_array($lang, self::SUPPORT_LANG)){

            App::setLocale($lang);            

            $response = new Response('Set Cookie');
            $response->withCookie(cookie('lang', $lang, 120, '/'));
            return [$response];
        } else {
            return [false, new Error('This lang not supporting')];
        }
    }
}

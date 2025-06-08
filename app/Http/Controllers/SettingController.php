<?php

namespace App\Http\Controllers;

use App\Services\SettingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SettingController extends Controller
{
    private SettingService $settingService;

    function __construct()
    {
        $this->settingService = new SettingService();
    }

    public function setLang(Request $request)
    {
        $lang = $request->get('lang');

//        $result = $this->settingService->setLang($request, $lang);
//
        $newLang = $lang ?? config('app.locale');
        App::setLocale($newLang);
        \Illuminate\Support\Facades\Session::put('lang', $newLang);

        return true;

//        if (current($result)){
//            Log::debug('lang-1'.$lang);
//            $cookie = cookie('lang', $lang, 3600000, '/');
//
//            return redirect('/')->withCookie($cookie);
//        } else {
//            return redirect()->back()->withErrors($result[1]);
//        }
        
    }
}

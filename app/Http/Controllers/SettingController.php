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
        [$success, $error] = $this->settingService->setLang($request->get('lang'));

        return $success ? responseJson() : responseJson(false, $error);
    }
}

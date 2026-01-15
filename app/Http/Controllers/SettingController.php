<?php

namespace App\Http\Controllers;

use App\Models\Errors\CommonError;
use App\Services\SettingService;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    private SettingService $settingService;

    function __construct()
    {
        $this->settingService = new SettingService();

        $this->middleware('throttle:9,60');
    }

    public function setLang(Request $request)
    {
        $lang = $request->get('lang');
        if (!$lang)
            return responseJson(false, new CommonError(__('validation.required', 'lang'), 'value_not_set'));

        [$success, $error] = $this->settingService->setLang($lang);

        return $success ? responseJson() : responseJson(false, $error);
    }

    public function setTheme(Request $request)
    {
        [$newTheme, $error] = $this->settingService->setTheme();

        return $newTheme ? responseJson(result: $newTheme) : responseJson(false, $error);
    }

    public function setTypeOutput(Request $request)
    {
        $type = $request->get('type');
        if (!$type)
            return responseJson(false, new CommonError(__('validation.required', 'type'), 'value_not_set'));

        [$result, $error] = $this->settingService->setTypeOutput($type);
        return $result ? responseJson() : responseJson(false, $error);
    }
}

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
    }

    public function setLang(Request $request)
    {
        $lang = $request->get('lang');
        if (!$lang)
            return responseJson(false, new CommonError(__('services.options.lang_not_set'), 'lang_not_set'));

        [$success, $error] = $this->settingService->setLang($lang);

        return $success ? responseJson() : responseJson(false, $error);
    }

    public function setTheme(Request $request)
    {
        [$newTheme, $error] = $this->settingService->setTheme();

        return $newTheme ? responseJson(result: $newTheme) : responseJson(false, $error);
    }
}

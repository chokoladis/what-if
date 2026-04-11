<?php

namespace App\Http\Controllers;

use App\Exceptions\SettingException;
use App\Services\SettingService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SettingController extends Controller
{
    private SettingService $settingService;

    function __construct()
    {
        $this->settingService = new SettingService();

        $this->middleware('throttle:9,60');
    }

    public function setLang(Request $request): Response
    {
        $lang = $request->get('lang');
        if (!$lang)
            return responseJson(false,
                new \App\DTO\Errors\CommonError(
                    __('validation.required', ['lang']),
                    'value_not_set'
                )
            );

        [$success, $error] = $this->settingService->setLang($lang);

        return $success ? responseJson() : responseJson(false, $error);
    }

    public function setTheme(): Response
    {
        [$newTheme, $error] = $this->settingService->setTheme();

        return $newTheme ? responseJson(result: $newTheme) : responseJson(false, $error);
    }

    public function setTypeOutput(Request $request): Response
    {
        $type = $request->get('type');
        if (!$type)
            return responseJson(false,
                new \App\DTO\Errors\CommonError(__('validation.required', ['type']), 'value_not_set')
            );

        try {
            $this->settingService->setTypeOutput($type);
            return responseJson();
        } catch (SettingException $e) {
            return responseJson(false,
                new \App\DTO\Errors\CommonError($e->getMessage())
            );
        }
    }
}

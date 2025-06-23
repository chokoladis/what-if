<?php

namespace App\Services;

use App\Models\Errors\CommonError;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;

class SettingService {
    
    const LANG = ['ru', 'en'];
    const THEME = ['dark', 'light'];

    public function setLang(string $lang): array
    {
        if (in_array($lang, self::LANG)){
            App::setLocale($lang);
            Cookie::queue('lang', $lang, 36000000);

            return [true, null];
        } else {
            return [false, new CommonError(__('services.options.lang_not_support'), 'lang_not_support')];
        }
    }

    public function setTheme(): array
    {
        try {
            $theme = Cookie::get('theme', 'dark');
            $themes = self::THEME;

            $key = array_search($theme, $themes);
            unset($themes[$key]);

            $newTheme = current($themes);
            Cookie::queue('theme', $newTheme, 36000000);

            return [$newTheme, null];

        } catch (\Exception $e) {
            return [false, new CommonError($e->getMessage())];
        }
    }
}

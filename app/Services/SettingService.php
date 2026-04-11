<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\SettingException;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;

class SettingService
{

    const LANG = ['ru', 'en'];
    const THEME = ['dark', 'light'];

    /**
     * @param string $lang
     * @return array{bool, ?\App\DTO\Errors\CommonError}
     */
    public function setLang(string $lang): array
    {
        if (in_array($lang, self::LANG)) {
            App::setLocale($lang);
            Cookie::queue('lang', $lang, 36000000);

//            todo переделать на exception ?
            return [true, null];
        } else {
            return [false, new \App\DTO\Errors\CommonError(__('services.options.lang_not_support'), 'lang_not_support')];
        }
    }

    /**
     * @return array{false|string, ?\App\DTO\Errors\CommonError}
     */
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

        } catch (Exception $e) {
            return [false, new \App\DTO\Errors\CommonError($e->getMessage())];
        }
    }

    /**
     * @param string $type
     * @return true
     */
    public function setTypeOutput(string $type): true
    {
        if (!in_array($type, QuestionService::ITEMS_TYPE_OUTPUT)) {
            throw new SettingException(__('services.options.lang_not_support'));
        }

        Cookie::queue('items-type-output', $type, 36000000);
        return true;
    }
}

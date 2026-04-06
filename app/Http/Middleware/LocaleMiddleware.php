<?php

namespace App\Http\Middleware;

use App\Services\SettingService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class LocaleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $lang = $request->cookie('lang', 'en');

        if (is_string($lang) && in_array($lang, SettingService::LANG)) {
            App::setLocale($lang);
        }

        return $next($request);
    }
}

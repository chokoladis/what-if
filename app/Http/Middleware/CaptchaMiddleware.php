<?php

namespace App\Http\Middleware;

use App\Services\CaptchaService;
use App\Services\SettingService;
use Closure;
use Illuminate\Http\Request;

class CaptchaMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next)
    {
        if (\App\Tools\Option::isCaptchaSetOn()) {

            $captcha = new CaptchaService();
            [$success,] = $captcha->verify($request->get('h-captcha-response') ?? '');

            if (!$success) {
                if ($request->expectsJson()) {
                    return response([
                        'success' => false,
                        'message' => 'Captcha invalid',
                    ], 403);
                }

                return redirect()->back()->with('error', 'Captcha invalid')->withInput();
            }
        }

        return $next($request);
    }
}

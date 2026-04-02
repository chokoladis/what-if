<?php

namespace App\Http\Middleware;

use App\Services\CaptchaService;
use App\Tools\Option;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CaptchaMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next)
    {
        if (Option::isCaptchaSetOn()) {

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

<?php

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\CaptchaMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        // api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin' => AdminMiddleware::class,
            'captcha' => CaptchaMiddleware::class,
        ]);
    })
    ->withEvents([
        __DIR__.'/../app/Listeners',
    ])
    ->withExceptions(function (Exceptions $exceptions) {
//        [\App\Exceptions\Integration\InvalidCaptchaException]
//        if ($exceptions instanceof \App\Exceptions\InvalidCaptchaException) {
//            return redirect()->back()->with('error', $exception->getMessage());
//        }
    })->create();

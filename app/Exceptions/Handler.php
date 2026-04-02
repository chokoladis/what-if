<?php

namespace App\Exceptions;

use App\Exceptions\Integration\InvalidCaptchaException;
use Throwable;

class Handler extends \Illuminate\Foundation\Exceptions\Handler
{
    public function render($request, Throwable $e)
    {
//        todo use in callback __('services.integrations.invalid_captcha')
        if ($e instanceof InvalidCaptchaException) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return redirect()->back()->with('error', $e->getMessage());
        }

        return parent::render($request, $e);
    }
}

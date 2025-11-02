<?php

namespace App\Exceptions;

use App\Exceptions\Integration\InvalidCaptchaException;
use Throwable;

class Handler extends \Illuminate\Foundation\Exceptions\Handler
{
    public function render($request, Throwable $e)
    {
        if ($e instanceof InvalidCaptchaException) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            // Если это обычная web-форма
            return redirect()->back()->with('error', $e->getMessage());
        }

        return parent::render($request, $e);
    }
}

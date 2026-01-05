<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()->role !== 'admin') {
            if ($request->expectsJson()) {
                return response([
                    'success' => false,
                    'message' => __('system.permission_denied'),
                ], 403);
            }

            return redirect()->back()->with('error', __('system.permission_denied'));
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsAdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->user()->role) {
            return $next($request);
        }
        return response()->json(['message' => 'unauthorize'], 403);
    }
}

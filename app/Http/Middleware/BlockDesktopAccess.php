<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockDesktopAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $blockDesktopAccess = 1; // 1 = allow desktop access, 0 = block desktop access

        if (!$request->isMobile() && $blockDesktopAccess) {
            abort(403, 'Access denied');
        }

        return $next($request);
    }
}

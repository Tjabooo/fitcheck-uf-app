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
        $blockDesktopAccess = 1; // 1 = block desktop access, 0 = allow desktop access

        if (
            !preg_match('/Mobi|Android|iPhone|iPad/i', $_SERVER['HTTP_USER_AGENT']) &&
            $blockDesktopAccess &&
            !$request->is('skrivbord')
        ) {
            return response(redirect()->route('errors.desktop'));
        }

        return $next($request);
    }
}

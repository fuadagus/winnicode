<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->role !== 'admin') {
            auth()->logout();
            return redirect('/dashboard/login')->with('error', 'Anda tidak memiliki akses ke panel admin.');
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsUser
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->role !== 'user') {
            return redirect('/admin')->with('error', 'Hanya user biasa yang boleh mengakses panel ini.');
        }

        return $next($request);
    }
}

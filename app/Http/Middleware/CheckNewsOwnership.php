<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckNewsOwnership
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $newsId = $request->route('news');
        
        if ($newsId) {
            $news = News::find($newsId);
            
            if (!$news) {
                abort(404, 'News not found');
            }
            
            $user = Auth::user();
            
            // Super Admin dapat mengakses semua berita
            if ($user->hasRole('Super Admin')) {
                return $next($request);
            }
            
            // Editor dapat mengakses semua berita untuk edit dan publish
            if ($user->hasRole('Editor')) {
                return $next($request);
            }
            
            // Writer hanya dapat mengakses berita miliknya sendiri
            if ($user->hasRole('Writer') && $news->user_id === $user->id) {
                return $next($request);
            }
            
            // Jika tidak memenuhi kriteria di atas, tolak akses
            abort(403, 'You are not authorized to access this news.');
        }
        
        return $next($request);
    }
}

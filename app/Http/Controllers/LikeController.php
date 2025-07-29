<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\News;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function likeNews(Request $request, News $news)
    {
        if (!$request->session()->has('device_id')) {
            $deviceId = Str::uuid()->toString();
            $request->session()->put('device_id', $deviceId);
        } else {
            $deviceId = $request->session()->get('device_id');
        }

        $like = Like::where('device_id', $deviceId)->where('news_id', $news->id)->first();
        $isLiked = false;

        if ($like) {
            $like->delete();
            $isLiked = false;
        } else {
            Like::create([
                'device_id' => $deviceId,
                'news_id' => $news->id,
            ]);
            $isLiked = true;
        }

        // Get updated likes count
        $likesCount = $news->likes()->count();

        // Return JSON response for Inertia/AJAX requests
        if ($request->expectsJson() || $request->header('X-Inertia')) {
            return response()->json([
                'success' => true,
                'isLiked' => $isLiked,
                'likesCount' => $likesCount,
                'message' => $isLiked ? 'Berita berhasil disukai' : 'Like berhasil dibatalkan'
            ]);
        }

        return back();
    }
}

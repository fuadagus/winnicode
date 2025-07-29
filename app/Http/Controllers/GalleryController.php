<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\News;
use Inertia\Inertia;

class GalleryController extends Controller
{
    /**
     * Display random gallery from news images
     */
    public function index()
    {
        // Get random news with images
        $newsWithImages = News::where('status', 'Published')
                             ->whereNotNull('image')
                             ->where('image', '!=', '')
                             ->inRandomOrder()
                             ->limit(12)
                             ->get(['id', 'title', 'image', 'created_at']);

        // Format images for gallery
        $galleryItems = $newsWithImages->map(function ($news) {
            return [
                'id' => $news->id,
                'title' => $news->title,
                'image' => $news->image,
                'date' => $news->created_at->format('d M Y'),
                'url' => route('news.show', $news->id) // Use ID instead of slug
            ];
        });

        return Inertia::render('Gallery/Index', [
            'galleryItems' => $galleryItems
        ]);
    }

    /**
     * Get more random images for AJAX request
     */
    public function getRandomImages(Request $request)
    {
        $limit = $request->get('limit', 12);
        
        $newsWithImages = News::where('status', 'Published')
                             ->whereNotNull('image')
                             ->where('image', '!=', '')
                             ->inRandomOrder()
                             ->limit($limit)
                             ->get(['id', 'title', 'image', 'created_at']);

        $galleryItems = $newsWithImages->map(function ($news) {
            return [
                'id' => $news->id,
                'title' => $news->title,
                'image' => $news->image,
                'date' => $news->created_at->format('d M Y'),
                'url' => route('news.show', $news->id) // Use ID instead of slug
            ];
        });

        return response()->json($galleryItems);
    }
}

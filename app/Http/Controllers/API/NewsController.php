<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Location;
use App\Models\News;

class NewsController extends Controller
{
    public function index()
    {
        return News::with(['user', 'location'])->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required',
            'content' => 'required',
            'image_url' => 'nullable|string',
            'user_id' => 'required|exists:users,id',
            'location_id' => 'required|exists:locations,id',
        ]);

        return News::create($data);
    }

    public function show(News $news)
    {
        return $news->load(['user', 'location']);
    }

    public function update(Request $request, News $news)
    {
        $data = $request->only('title', 'content', 'image_url', 'user_id', 'location_id');
        $news->update($data);
        return $news;
    }

    public function destroy(News $news)
    {
        $news->delete();
        return response()->json(null, 204);
    }
}

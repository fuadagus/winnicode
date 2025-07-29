<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\User;
use App\Models\Category;
use App\Events\NewsCreated;
use Illuminate\Http\Request;
use App\Events\NewsStatusUpdated;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class NewsController extends Controller
{
    /**
     * Display home page with latest news - OPTIMIZED VERSION
     */
    public function index(Request $request)
    {
        $userLat = $request->get('lat');
        $userLng = $request->get('lng');
        $filter = $request->get('filter');
        
        try {
            // Simplified query for better performance
            $newsQuery = News::where('status', 'Published')
                ->select(['id', 'title', 'content', 'image', 'created_at', 'user_id', 'category_id', 'views', 'city', 'province'])
                ->with(['author:id,name', 'category:id,name'])
                ->latest()
                ->take(20);

            // Simplified data for faster loading
            $latestNews = $newsQuery->get();
            
            // Format news data
            $latestNews = $latestNews->map(function ($news) {
                $news->author_name = $news->author->name ?? 'Unknown';
                $news->location = $this->getLocationString($news);
                return $news;
            });

            return Inertia::render('Home', [
                'latestNews' => $latestNews,
                'popularNews' => $latestNews->take(10), // Use same data for now
                'topCategories' => [], // Simplified for performance
                'filterType' => $filter ?? null,
            ]);
        } catch (\Exception $e) {
            // Return error information
            return Inertia::render('Home', [
                'latestNews' => [],
                'popularNews' => [],
                'topCategories' => [],
                'error' => $e->getMessage(),
                'debug' => 'Error occurred in controller'
            ]);
        }
    }
    
    /**
     * Helper method to get location string
     */
    private function getLocationString($news)
    {
        if ($news->city && $news->province) {
            return $news->city . ', ' . $news->province;
        } elseif ($news->city) {
            return $news->city;
        } elseif ($news->province) {
            return $news->province;
        }
        return null;
    }
    
    /**
     * Get news sorted by proximity to user location
     */
    private function getNewsByProximity($query, $userLat, $userLng, $type = 'latest', $limit = null)
    {
        $news = $query->get();
        
        // Calculate distance for each news
        $newsWithDistance = $news->map(function ($item) use ($userLat, $userLng) {
            $item->distance = $item->distanceFrom($userLat, $userLng);
            return $item;
        });
        
        // Separate local and non-local news
        $localNews = $newsWithDistance->filter(function ($item) {
            return $item->distance !== null && $item->distance <= 100; // Within 100km
        });
        
        $nonLocalNews = $newsWithDistance->filter(function ($item) {
            return $item->distance === null || $item->distance > 100;
        });
        
        // Sort local news by distance first, then by type
        switch ($type) {
            case 'views':
                $sortedLocal = $localNews->sortBy([
                    ['distance', 'asc'],
                    ['views', 'desc']
                ]);
                $sortedNonLocal = $nonLocalNews->sortByDesc('views');
                break;
                
            case 'likes':
                $localNews->load('likes');
                $nonLocalNews->load('likes');
                
                $sortedLocal = $localNews->sortBy([
                    ['distance', 'asc']
                ])->sortByDesc(function ($item) {
                    return $item->likes->count();
                });
                
                $sortedNonLocal = $nonLocalNews->sortByDesc(function ($item) {
                    return $item->likes->count();
                });
                break;
                
            default: // latest
                $sortedLocal = $localNews->sortBy([
                    ['distance', 'asc'],
                    ['created_at', 'desc']
                ]);
                $sortedNonLocal = $nonLocalNews->sortByDesc('created_at');
                break;
        }
        
        // Merge: local news first, then non-local
        $result = $sortedLocal->merge($sortedNonLocal);
        
        // Apply limit if specified
        if ($limit) {
            return $result->take($limit);
        }
        
        return $result;
    }
    
    /**
     * Get nearby news within specified radius
     */
    public function getNearbyNews(Request $request)
    {
        $userLat = $request->get('lat');
        $userLng = $request->get('lng');
        $radius = $request->get('radius', 50); // Default 50km
        $limit = $request->get('limit', 10); // Default 10 berita
        
        if (!$userLat || !$userLng) {
            return response()->json([
                'success' => false,
                'message' => 'Latitude and longitude are required'
            ]);
        }
        
        $news = News::where('status', 'Published')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();
            
        $nearbyNews = $news->filter(function ($item) use ($userLat, $userLng, $radius) {
            $distance = $item->distanceFrom($userLat, $userLng);
            return $distance !== null && $distance <= $radius;
        })->map(function ($item) use ($userLat, $userLng) {
            $item->distance = $item->distanceFrom($userLat, $userLng);
            return $item;
        })->sortBy('distance')->take($limit);
        
        return response()->json([
            'success' => true,
            'data' => $nearbyNews->values()->map(function ($news) {
                return [
                    'id' => $news->id,
                    'title' => $news->title,
                    'excerpt' => Str::limit(strip_tags($news->content), 150),
                    'image' => $news->image ? asset('storage/images/' . $news->image) : asset('img/noimg.jpg'),
                    'author' => $news->author->name,
                    'category' => $news->category->name,
                    'location' => $news->location,
                    'distance' => round($news->distance, 2),
                    'created_at' => $news->created_at->format('Y-m-d H:i:s'),
                    'url' => route('news.show', $news->id)
                ];
            })
        ]);
    }

    public function manage()
    {
        $user = auth()->user();
        
        if ($user->hasRole('Super Admin')) {
            // Super Admin dapat melihat semua berita
            $allNews = News::with(['category', 'author'])->latest()->get();
        } elseif ($user->hasRole('Editor')) {
            // Editor dapat melihat semua berita
            $allNews = News::with(['category', 'author'])->latest()->get();
        } else {
            // Writer hanya dapat melihat berita miliknya
            $allNews = News::with(['category', 'author'])
                          ->where('user_id', $user->id)
                          ->latest()
                          ->get();
        }
        
        return view('admin.news.manage', compact('allNews'));
    }

    public function viewCategory(Category $categories)
    {
        $latestNews = $categories->news()->where('status', 'Published')->latest()->get();
        $topNews = $categories->news()->where('status', 'Published')->orderBy('views', 'desc')->get();
        $popularNews = $categories->news()
            ->where('status', 'Published')
            ->withCount('likes')
            ->orderBy('likes_count', 'desc')
            ->get();

        $categories->increment('views');

        return view('viewCategory', compact('categories', 'latestNews', 'topNews', 'popularNews'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $allCategory = Category::all();
        return view('news.create', compact('allCategory'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|min:1',
                'content' => 'required|string|min:1',
                'image' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
                'category_id' => 'required|exists:category,id',
                'city' => 'nullable|string|max:100',
                'province' => 'nullable|string|max:100',
                'country' => 'nullable|string|max:100',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
            ]);

            $imageHashName = null;

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageHashName = $image->hashName();
                $image->storeAs('public/images', $imageHashName);
            }

            $news = News::create([
                'title' => $request->title,
                'content' => $request->content,
                'user_id' => Auth::id(),
                'category_id' => $request->category_id,
                'image' => $imageHashName,
                'city' => $request->city,
                'province' => $request->province,
                'country' => $request->country ?: 'Indonesia',
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]);

            event(new NewsCreated($news));

            return response()->json([
                'success' => true,
                'message' => 'Successfully saved the data.',
                'redirect_url' => route('dashboard')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(News $news)
    {
        // Increment views
        $news->increment('views');

        // Load necessary relationships
        $news->load(['author', 'category', 'likes']);

        // Get related news (same category, excluding current news)
        $relatedNews = News::where('status', 'Published')
            ->where('category_id', $news->category_id)
            ->where('id', '!=', $news->id)
            ->with(['author', 'category'])
            ->withCount('likes')
            ->latest()
            ->take(5)
            ->get();

        // Get popular news (most viewed)
        $popularNews = News::where('status', 'Published')
            ->where('id', '!=', $news->id)
            ->with(['author', 'category'])
            ->withCount('likes')
            ->orderBy('views', 'desc')
            ->take(5)
            ->get();

        // Get more articles for bottom section
        $moreArticles = News::where('status', 'Published')
            ->where('id', '!=', $news->id)
            ->with(['author', 'category'])
            ->withCount('likes')
            ->inRandomOrder()
            ->take(6)
            ->get();

        // Add location string for all news items
        $relatedNews = $relatedNews->map(function ($item) {
            $item->location = $this->getLocationString($item);
            return $item;
        });

        $popularNews = $popularNews->map(function ($item) {
            $item->location = $this->getLocationString($item);
            return $item;
        });

        $moreArticles = $moreArticles->map(function ($item) {
            $item->location = $this->getLocationString($item);
            return $item;
        });

        // Add location to main news
        $news->location = $this->getLocationString($news);

        // Check if request wants Inertia response
        if (request()->wantsJson() || request()->header('X-Inertia')) {
            return Inertia::render('NewsDetail', [
                'news' => $news,
                'relatedNews' => $relatedNews,
                'popularNews' => $popularNews,
                'moreArticles' => $moreArticles
            ]);
        }

        // Fallback for traditional blade view
        $randomNews = News::inRandomOrder()->take(2)->get();
        return view('detail', compact('news', 'randomNews'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(News $news)
    {
        $allCategory = Category::all();
        $user = auth()->user();
        
        // Cek role untuk akses edit
        if ($user->hasRole('Writer') && $news->user_id !== $user->id) {
            return redirect()->back()->with('error', 'You can only edit your own news.');
        }
        
        if ($user->hasRole('Writer') && !in_array($news->status, ['Draft', 'Pending'])) {
            return redirect()->back()->with('error', 'You can only edit news that are in Draft or Pending status.');
        }

        return view('news.edit', compact('news', 'allCategory'));
    }

    /**
     * Show the form for editing news with admin privileges (including status)
     */
    public function adminEdit(News $news)
    {
        $user = auth()->user();
        
        // Hanya Super Admin dan Editor yang dapat mengakses
        if (!$user->hasAnyRole(['Super Admin', 'Editor'])) {
            abort(403, 'Unauthorized access.');
        }
        
        $categories = Category::all();
        $canPublish = Auth::user()->hasRole(['Super Admin', 'Editor']);

        return Inertia::render('Admin/News/Edit', [
            'news' => $news,
            'categories' => $categories,
            'canPublish' => $canPublish,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, News $news)
    {
        try {
            $user = auth()->user();
            
            // Cek ownership untuk Writer
            if ($user->hasRole('Writer') && $news->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can only edit your own news.'
                ]);
            }
            
            $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'image' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
                'category_id' => 'required|exists:category,id',
                'city' => 'nullable|string|max:100',
                'province' => 'nullable|string|max:100',
                'country' => 'nullable|string|max:100',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
            ]);

            $data = [
                'title' => $request->title,
                'content' => $request->content,
                'category_id' => $request->category_id,
                'city' => $request->city,
                'province' => $request->province,
                'country' => $request->country ?: 'Indonesia',
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ];

            // Writer tidak dapat mengubah status, reset ke Draft jika diubah
            if ($user->hasRole('Writer')) {
                $data['status'] = 'Draft';
            }

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $image->storeAs('public/images/', $image->hashName());

                if ($news->image) {
                    Storage::delete('public/images/' . $news->image);
                }

                $data['image'] = $image->hashName();
            }

            $news->update($data);

            event(new NewsCreated($news));

            return response()->json([
                'success' => true,
                'message' => 'Successfully updated the news.',
                'redirect_url' => route('dashboard')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Update news with admin privileges (including status)
     */
    public function adminUpdate(Request $request, News $news)
    {
        $user = auth()->user();
        
        // Hanya Super Admin dan Editor yang dapat mengakses
        if (!$user->hasAnyRole(['Super Admin', 'Editor'])) {
            abort(403, 'Unauthorized access.');
        }
        
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'required|exists:category,id',
            'status' => 'required|in:draft,pending,published',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'slug' => 'nullable|string|unique:news,slug,' . $news->id,
        ]);

        $data = $request->except(['image']);
        
        // Convert status to database format (capitalize first letter)
        $data['status'] = ucfirst($request->status);
        
        // Handle slug
        if ($request->slug && $request->slug !== $news->slug) {
            $slug = $request->slug;
        } elseif ($request->title !== $news->title) {
            $slug = Str::slug($request->title);
        } else {
            $slug = $news->slug;
        }
        
        // Ensure slug is unique
        $originalSlug = $slug;
        $counter = 1;
        while (News::where('slug', $slug)->where('id', '!=', $news->id)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        $data['slug'] = $slug;

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($news->image) {
                Storage::delete('public/images/' . $news->image);
            }
            
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->storeAs('public/images', $imageName);
            $data['image'] = $imageName;
        }

        // Set published_at if status is published
        if ($data['status'] === 'Published' && $news->status !== 'Published') {
            $data['published_at'] = $request->published_at ?: now();
        }

        $oldStatus = $news->status;
        $news->update($data);

        // Fire event if status changed
        if ($oldStatus !== $data['status']) {
            event(new NewsStatusUpdated($news, $oldStatus, $data['status']));
        }

        return redirect()->route('admin.news.index')->with('success', 'Berita berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(News $news)
    {
        try {
            $user = auth()->user();
            
            // Super Admin dapat menghapus semua berita
            if ($user->hasRole('Super Admin')) {
                if ($news->image) {
                    Storage::delete('public/images/' . $news->image);
                }
                $news->delete();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Successfully deleted the news.',
                    'redirect_url' => route('admin.news.manage')
                ]);
            }
            
            // Writer hanya dapat menghapus berita miliknya
            if ($user->hasRole('Writer') && $news->user_id === $user->id) {
                if ($news->image) {
                    Storage::delete('public/images/' . $news->image);
                }
                $news->delete();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Successfully deleted your news.',
                    'redirect_url' => route('dashboard')
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to delete this news.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function status(Request $request, News $news)
    {
        $user = auth()->user();
        
        if ($user->hasRole('Super Admin')) {
            $draftNews = News::with('category')->whereIn('status', ['Pending', 'Draft'])->get();
        } elseif ($user->hasRole('Editor')) {
            $draftNews = News::with('category')->whereIn('status', ['Pending', 'Draft'])->get();
        } else {
            $draftNews = News::with('category')
                           ->whereIn('status', ['Pending', 'Draft'])
                           ->where('user_id', $user->id)
                           ->get();
        }

        return view('news.status', compact('draftNews'));
    }

    public function view(Request $request, News $news)
    {
        return view('news.view', compact('news'));
    }

    public function updateStatus(Request $request, News $news)
    {
        try {
            $user = auth()->user();
            
            // Hanya Editor dan Super Admin yang dapat mengubah status
            if (!$user->hasAnyRole(['Super Admin', 'Editor'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized to change news status.'
                ]);
            }
            
            $request->validate([
                'status' => 'required|in:Published,Draft,Pending'
            ]);

            $news->status = $request->status;
            $news->save();

            event(new NewsStatusUpdated($news));

            return response()->json([
                'success' => true,
                'message' => 'Successfully updated news status.',
                'redirect_url' => route('news.status')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function draft()
    {
        $userId = auth()->id();

        $acceptedNews = News::with('category')
            ->where('status', 'Published')
            ->where('user_id', $userId)
            ->get();

        $notAcceptedNews = News::with('category')
            ->whereIn('status', ['Pending', 'Draft', 'Reject'])
            ->where('user_id', $userId)
            ->get();

        // Check if request expects JSON (Vue.js)
        if (request()->wantsJson() || request()->header('X-Inertia')) {
            return \Inertia\Inertia::render('Admin/Users/Draft', [
                'acceptedNews' => $acceptedNews,
                'notAcceptedNews' => $notAcceptedNews,
                'currentUser' => auth()->user()
            ]);
        }

        // Return Blade view for legacy support
        return view('admin.users.draft', compact('acceptedNews', 'notAcceptedNews'));
    }

    /**
     * Search news articles
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        
        if (empty($query)) {
            return redirect()->route('home');
        }

        $news = News::where('status', 'Published')
            ->where(function ($q) use ($query) {
                $q->where('title', 'LIKE', '%' . $query . '%')
                  ->orWhere('content', 'LIKE', '%' . $query . '%');
            })
            ->with(['author', 'category'])
            ->withCount('likes')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        // Add location for each news
        $news->getCollection()->transform(function ($item) {
            $item->location = $this->getLocationString($item);
            return $item;
        });

        if ($request->expectsJson() || $request->header('X-Inertia')) {
            return Inertia::render('SearchResults', [
                'news' => $news,
                'query' => $query
            ]);
        }

        return view('search', compact('news', 'query'));
    }

    /**
     * Get trending news for API
     */
    public function getTrendingNews()
    {
        $trending = News::where('status', 'Published')
            ->with(['author', 'category'])
            ->withCount('likes')
            ->orderBy('views', 'desc')
            ->take(4)
            ->get();

        return response()->json($trending);
    }

    // Admin Methods
    public function adminIndex(Request $request)
    {
        $query = News::with(['author', 'category'])->withCount('likes');

        // Apply filters
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('content', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->category) {
            $query->where('category_id', $request->category);
        }

        if ($request->author) {
            $query->where('user_id', $request->author);
        }

        // Apply sorting
        switch ($request->sort) {
            case 'oldest':
                $query->oldest();
                break;
            case 'title':
                $query->orderBy('title');
                break;
            case 'views':
                $query->orderBy('views', 'desc');
                break;
            case 'likes':
                $query->orderBy('likes_count', 'desc');
                break;
            default:
                $query->latest();
        }

        $news = $query->paginate(15);
        $categories = Category::all();
        $authors = User::role(['Super Admin', 'Editor', 'Writer'])->get();
        
        // Stats
        $stats = [
            'total' => News::count(),
            'published' => News::where('status', 'Published')->count(),
            'pending' => News::where('status', 'Pending')->count(),
            'draft' => News::where('status', 'Draft')->count(),
        ];

        return Inertia::render('Admin/News/Index', [
            'news' => $news,
            'categories' => $categories,
            'authors' => $authors,
            'stats' => $stats,
        ]);
    }

    public function adminCreate(Request $request)
    {
        $categories = Category::all();
        $selectedCategory = $request->get('category');
        $canPublish = Auth::user()->hasRole(['Super Admin', 'Editor']);

        return Inertia::render('Admin/News/Create', [
            'categories' => $categories,
            'selectedCategory' => $selectedCategory,
            'canPublish' => $canPublish,
        ]);
    }

    public function adminStore(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'required|exists:category,id',
            'status' => 'required|in:draft,pending,published',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'slug' => 'nullable|string|unique:news,slug',
        ]);

        // Auto-generate slug if not provided
        $slug = $request->slug ?: Str::slug($request->title);
        $originalSlug = $slug;
        $counter = 1;
        
        while (News::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $data = $request->except(['image']);
        $data['slug'] = $slug;
        $data['user_id'] = Auth::id();
        
        // Convert status to database format (capitalize first letter)
        $data['status'] = ucfirst($request->status);

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->storeAs('public/images', $imageName);
            $data['image'] = $imageName;
        }

        // Set published_at if status is published
        if ($data['status'] === 'Published') {
            $data['published_at'] = $request->published_at ?: now();
        }

        $news = News::create($data);

        // Fire event
        event(new NewsCreated($news));

        return redirect()->route('admin.news.index')->with('success', 'Berita berhasil dibuat.');
    }

    public function adminShow(News $news)
    {
        $news->load(['user', 'category', 'likes']);
        
        return Inertia::render('Admin/News/Show', [
            'news' => $news,
        ]);
    }

    public function adminDestroy(News $news)
    {
        // Delete image if exists
        if ($news->image) {
            Storage::delete('public/images/' . $news->image);
        }

        $news->delete();

        return redirect()->route('admin.news.index')->with('success', 'Berita berhasil dihapus.');
    }

    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:news,id',
            'status' => 'required|in:draft,pending,published',
        ]);

        // Convert status to database format (capitalize first letter)
        $status = ucfirst($request->status);
        
        $count = News::whereIn('id', $request->ids)->update(['status' => $status]);

        return redirect()->back()->with('success', "{$count} berita berhasil diperbarui.");
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:news,id',
        ]);

        $news = News::whereIn('id', $request->ids)->get();
        
        // Delete images
        foreach ($news as $item) {
            if ($item->image) {
                Storage::delete('public/images/' . $item->image);
            }
        }

        $count = News::whereIn('id', $request->ids)->delete();

        return redirect()->back()->with('success', "{$count} berita berhasil dihapus.");
    }
}

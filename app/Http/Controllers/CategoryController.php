<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\News;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::withCount('news')->get();
        
        return Inertia::render('Admin/Categories/Index', [
            'categories' => $categories,
        ]);
    }
    
    public function view(Category $category, Request $request)
    {
        $query = News::where('category_id', $category->id)
                    ->with(['author', 'category']);

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
            default:
                $query->latest();
        }

        $perPage = $request->perPage ?: 10;
        $news = $query->get(); // Changed from paginate to get for Vue component
        $authors = User::role(['Super Admin', 'Editor', 'Writer'])->get();

        return Inertia::render('Admin/Categories/View', [
            'category' => $category,
            'news' => $news,
            'authors' => $authors,
        ]);
    }

    public function manage()
    {
        $allCategory = Category::all();
        return view('admin.category.manage', compact('allCategory'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|min:5',
            ]);

            Category::create([
                'name' => $request->name,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Successfully saved the data.',
                'redirect_url' => route('admin.category.manage')
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
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        try {
            $request->validate([
                // Lebih baik gunakan name, karena jika menggunakan id, saat di update dengan nama yang sama, maka akan berhasil
                'name' => 'required|string|max:255|unique:category,name,' . $category->name,
            ]);

            $category->update([
                'name' => $request->name,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Successfully update the data.',
                'redirect_url' => route('admin.category.manage')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        try {
            $category->delete();

            return response()->json([
                'success' => true,
                'message' => 'Successfully delete the data.',
                'redirect_url' => route('admin.category.manage')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}

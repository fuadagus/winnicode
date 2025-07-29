<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\News;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalUsers = User::count();
        $totalNews = News::count();
        $totalNewsAccepted = News::where('status', 'Published')->count();
        $totalNewsNotAccepted = News::whereIn('status', ['Pending', 'Draft'])->count();

        // Chart JS
        $usersPerMonth = [];
        $newsPerMonth = [];

        for ($month = 1; $month <= 12; $month++) {
            $startOfMonth = Carbon::create(null, $month)->startOfMonth();
            $endOfMonth = Carbon::create(null, $month)->endOfMonth();

            $usersPerMonth[] = User::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
            $newsPerMonth[] = News::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
        }

        $currentMonth = Carbon::now()->month;
        $totalUsersCurrentMonth = $usersPerMonth[$currentMonth - 1];
        $totalNewsCurrentMonth = $newsPerMonth[$currentMonth - 1];

        // Recent news for dashboard
        $recentNews = News::with(['author', 'category'])
            ->latest()
            ->take(10)
            ->get();

        // Categories with news count
        $categories = Category::withCount('news')->get();

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'totalUsers' => $totalUsers,
                'totalNews' => $totalNews,
                'publishedNews' => $totalNewsAccepted,
                'pendingNews' => News::where('status', 'Pending')->count(),
                'draftNews' => News::where('status', 'Draft')->count(),
                'totalNewsAccepted' => $totalNewsAccepted,
                'totalNewsNotAccepted' => $totalNewsNotAccepted,
                'totalUsersCurrentMonth' => $totalUsersCurrentMonth,
                'totalNewsCurrentMonth' => $totalNewsCurrentMonth,
            ],
            'recentNews' => $recentNews,
            'categories' => $categories,
            'categoryStats' => $categories,
            'chartData' => [
                'usersPerMonth' => $usersPerMonth,
                'newsPerMonth' => $newsPerMonth,
            ]
        ]);
    }
}
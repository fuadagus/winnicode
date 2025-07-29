<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\GalleryController;

// All
Route::get('/', [NewsController::class, 'index'])->name('home');
Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery.index');
Route::get('/api/nearby-news', [NewsController::class, 'getNearbyNews'])->name('api.nearby-news');
Route::get('/news/{news}/show', [NewsController::class, 'show'])->name('news.show');
Route::post('/news/{news}/like', [LikeController::class, 'likeNews'])->name('news.like');
Route::get('/news/{categories}/category', [NewsController::class, 'viewCategory'])->name('news.viewCategory');

// Guest
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [LoginController::class, 'login'])->name('login');
    Route::post('/login/submit', [LoginController::class, 'loginSubmit'])->name('login.submit');
    Route::get('/register', [LoginController::class, 'register'])->name('register');
    Route::post('/register/submit', [LoginController::class, 'registerSubmit'])->name('register.submit');
});

// Auth
Route::middleware(['auth', 'online.status'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    // News
    Route::get('/news/{news}/view', [NewsController::class, 'view'])->name('news.view');
    // Profile
    Route::resource('profile', UserController::class)->parameters([
        'profile' => 'user'
    ])->only([
        'edit',
        'update'
    ]);
    // Notification
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/count', [NotificationController::class, 'unreadNotificationsCount'])->name('count');
        Route::get('/fetch', [NotificationController::class, 'fetchNotifications'])->name('fetch');
        Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('markAsRead');
    });
});

// Super Admin
Route::middleware(['role:Super Admin'])->group(function () {
    // News Admin Routes
    Route::get('/admin/news', [NewsController::class, 'adminIndex'])->name('admin.news.index');
    Route::get('/admin/news/create', [NewsController::class, 'adminCreate'])->name('admin.news.create');
    Route::get('/admin/news/manage', [NewsController::class, 'manage'])->name('admin.news.manage');
    Route::post('/admin/news', [NewsController::class, 'adminStore'])->name('admin.news.store');
    Route::post('/admin/news/bulk-update', [NewsController::class, 'bulkUpdate'])->name('admin.news.bulk-update');
    Route::delete('/admin/news/bulk-delete', [NewsController::class, 'bulkDelete'])->name('admin.news.bulk-delete');
    Route::get('/admin/news/{news}', [NewsController::class, 'adminShow'])->name('admin.news.show');
    Route::get('/admin/news/{news}/edit', [NewsController::class, 'adminEdit'])->name('admin.news.edit');
    Route::put('/admin/news/{news}', [NewsController::class, 'adminUpdate'])->name('admin.news.update');
    Route::delete('/admin/news/{news}', [NewsController::class, 'adminDestroy'])->name('admin.news.destroy');
    // Category
    Route::resource('admin/category', CategoryController::class)->names('admin.category')->only([
        'store',
        'update',
        'destroy'
    ]);
    Route::get('/admin/category/manage', [CategoryController::class, 'manage'])->name('admin.category.manage');
    // Users
    Route::resource('admin/users', UserController::class)->only(['index', 'destroy'])
        ->names([
            'index' => 'admin.users.manage',
            'destroy' => 'admin.users.destroy'
        ]);

    Route::patch('/admin/users/{user}/assignRole', [UserController::class, 'assignRole'])->name('admin.users.assignRole');
});

// Editor
Route::group(['middleware' => ['permission:Status News|Update Status News']], function () {
    Route::get('/news/status', [NewsController::class, 'status'])->name('news.status');
    Route::patch('/news/{news}/updatestatus', [NewsController::class, 'updateStatus'])->name('news.updateStatus');
});

// Writer
Route::group(['middleware' => ['permission:Create News|Store News|Edit News|Update News|Draft']], function () {
    Route::resource('news', NewsController::class)->names('news')->only([
        'create',
        'store',
        'edit',
        'update'
    ]);
    Route::get('/news/draft', [NewsController::class, 'draft'])->name('news.draft');
});

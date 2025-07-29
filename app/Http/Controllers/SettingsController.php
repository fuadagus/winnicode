<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use App\Models\News;
use App\Models\Category;

class SettingsController extends Controller
{
    /**
     * Display system settings
     */
    public function index()
    {
        $systemStats = [
            'total_users' => User::count(),
            'active_users' => User::whereNotNull('last_seen_at')
                                 ->where('last_seen_at', '>=', now()->subMinutes(5))
                                 ->count(),
            'total_news' => News::count(),
            'published_news' => News::where('status', 'published')->count(),
            'draft_news' => News::where('status', 'draft')->count(),
            'total_categories' => Category::count(),
            'storage_usage' => $this->getStorageUsage(),
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
        ];

        $settings = [
            'site_name' => config('app.name'),
            'site_url' => config('app.url'),
            'admin_email' => config('mail.from.address'),
            'timezone' => config('app.timezone'),
            'debug_mode' => config('app.debug'),
            'environment' => config('app.env'),
        ];

        return Inertia::render('Admin/Settings/Index', [
            'systemStats' => $systemStats,
            'settings' => $settings
        ]);
    }

    /**
     * Update system settings
     */
    public function update(Request $request)
    {
        $request->validate([
            'site_name' => 'required|string|max:255',
            'admin_email' => 'required|email',
            'timezone' => 'required|string',
        ]);

        // Note: In a real application, you would update these in a settings table
        // or configuration file. For this demo, we'll just return success
        
        return redirect()->back()->with('success', 'Pengaturan berhasil disimpan');
    }

    /**
     * Clear application cache
     */
    public function clearCache()
    {
        try {
            \Artisan::call('cache:clear');
            \Artisan::call('config:clear');
            \Artisan::call('view:clear');
            \Artisan::call('route:clear');

            return redirect()->back()->with('success', 'Cache berhasil dibersihkan');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membersihkan cache: ' . $e->getMessage());
        }
    }

    /**
     * Get storage usage information
     */
    private function getStorageUsage()
    {
        $path = storage_path('app');
        
        if (is_dir($path)) {
            $size = $this->getDirSize($path);
            return $this->formatBytes($size);
        }
        
        return 'Unknown';
    }

    /**
     * Get directory size
     */
    private function getDirSize($directory)
    {
        $size = 0;
        $files = glob(rtrim($directory, '/').'/*', GLOB_NOSORT);
        
        foreach ($files as $file) {
            $size += is_file($file) ? filesize($file) : $this->getDirSize($file);
        }
        
        return $size;
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($size, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        
        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        
        return round($size, $precision) . ' ' . $units[$i];
    }
}

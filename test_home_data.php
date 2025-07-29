<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing News untuk home page:\n\n";

echo "Latest News (Published status):\n";
$latestNews = App\Models\News::where('status', 'Published')->latest()->get();
echo "Jumlah: " . $latestNews->count() . "\n";
if ($latestNews->count() > 0) {
    echo "Contoh judul: " . $latestNews->first()->title . "\n";
}

echo "\nTop News (Published status, sorted by views):\n";
$topNews = App\Models\News::where('status', 'Published')->orderBy('views', 'desc')->get();
echo "Jumlah: " . $topNews->count() . "\n";
if ($topNews->count() > 0) {
    echo "Contoh judul: " . $topNews->first()->title . "\n";
    echo "Views: " . $topNews->first()->views . "\n";
}

echo "\nPopular News (Published status, sorted by likes):\n";
$popularNews = App\Models\News::where('status', 'Published')
    ->withCount('likes')
    ->orderBy('likes_count', 'desc')
    ->get();
echo "Jumlah: " . $popularNews->count() . "\n";
if ($popularNews->count() > 0) {
    echo "Contoh judul: " . $popularNews->first()->title . "\n";
    echo "Likes: " . $popularNews->first()->likes_count . "\n";
}

echo "\nTop Categories:\n";
$topCategory = App\Models\Category::orderBy('views', 'desc')
    ->orderBy('created_at', 'desc')
    ->get();
echo "Jumlah: " . $topCategory->count() . "\n";
if ($topCategory->count() > 0) {
    echo "Contoh kategori: " . $topCategory->first()->name . "\n";
    echo "Views: " . $topCategory->first()->views . "\n";
}

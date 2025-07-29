<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\News;

echo "Total news articles: " . News::count() . "\n";
echo "\nLatest 5 news with images:\n";
echo "================================\n";

$latestNews = News::orderBy('id', 'desc')->take(5)->get();

foreach ($latestNews as $news) {
    echo "ID: {$news->id}\n";
    echo "Title: " . substr($news->title, 0, 60) . "...\n";
    echo "Image: {$news->image}\n";
    echo "Location: {$news->location}\n";
    echo "--------------------------------\n";
}

echo "\nNews by category:\n";
echo "=================\n";
$categories = News::selectRaw('category_id, COUNT(*) as count')
    ->groupBy('category_id')
    ->with('category')
    ->get();

foreach ($categories as $category) {
    echo $category->category->name . ": " . $category->count . " articles\n";
}

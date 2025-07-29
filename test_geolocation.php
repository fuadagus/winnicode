<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\News;

echo "=== CHECKING NEWS GEOLOCATION DATA ===\n\n";

// Total news
$totalNews = News::count();
echo "Total news: $totalNews\n";

// Published news
$publishedNews = News::where('status', 'Published')->count();
echo "Published news: $publishedNews\n";

// News with coordinates
$newsWithCoords = News::whereNotNull('latitude')->whereNotNull('longitude')->count();
echo "News with coordinates: $newsWithCoords\n";

// Published news with coordinates
$publishedWithCoords = News::where('status', 'Published')
    ->whereNotNull('latitude')
    ->whereNotNull('longitude')
    ->count();
echo "Published news with coordinates: $publishedWithCoords\n\n";

// Sample news with coordinates
echo "=== SAMPLE NEWS WITH COORDINATES ===\n";
$sampleNews = News::where('status', 'Published')
    ->whereNotNull('latitude')
    ->whereNotNull('longitude')
    ->select('id', 'title', 'latitude', 'longitude', 'city', 'province')
    ->take(5)
    ->get();

foreach ($sampleNews as $news) {
    echo "ID: {$news->id}\n";
    echo "Title: {$news->title}\n";
    echo "Latitude: {$news->latitude}\n";
    echo "Longitude: {$news->longitude}\n";
    echo "Location: {$news->city}, {$news->province}\n";
    echo "---\n";
}

// Test nearby calculation with sample coordinates (Jakarta area)
echo "\n=== TESTING NEARBY CALCULATION ===\n";
$testLat = -6.2088; // Jakarta latitude
$testLng = 106.8456; // Jakarta longitude

echo "Testing with coordinates: $testLat, $testLng\n";

$nearbyNews = collect();
foreach ($sampleNews as $news) {
    if ($news->latitude && $news->longitude) {
        $distance = $news->distanceFrom($testLat, $testLng);
        if ($distance <= 100) { // Within 100km
            $nearbyNews->push([
                'id' => $news->id,
                'title' => $news->title,
                'distance' => round($distance, 2)
            ]);
        }
    }
}

echo "Nearby news found: " . $nearbyNews->count() . "\n";
foreach ($nearbyNews as $news) {
    echo "- {$news['title']} ({$news['distance']} km)\n";
}

echo "\n=== TEST COMPLETED ===\n";

<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Category;
use App\Models\News;

echo "=== CHECKING CATEGORIES ===\n\n";

// Total categories
$totalCategories = Category::count();
echo "Total categories: $totalCategories\n";

if ($totalCategories > 0) {
    echo "\n=== CATEGORY LIST ===\n";
    $categories = Category::withCount('news')->get();
    
    foreach ($categories as $category) {
        echo "ID: {$category->id}\n";
        echo "Name: {$category->name}\n";
        echo "News count: {$category->news_count}\n";
        echo "Views: {$category->views}\n";
        echo "---\n";
    }
} else {
    echo "\n❌ NO CATEGORIES FOUND!\n";
    echo "Creating sample categories...\n\n";
    
    $sampleCategories = [
        'Politik',
        'Ekonomi', 
        'Olahraga',
        'Teknologi',
        'Kesehatan',
        'Pendidikan',
        'Hiburan',
        'Internasional'
    ];
    
    foreach ($sampleCategories as $categoryName) {
        Category::create(['name' => $categoryName]);
        echo "✅ Created category: $categoryName\n";
    }
    
    echo "\n=== CATEGORIES CREATED SUCCESSFULLY ===\n";
}

// Check news without categories
$newsWithoutCategory = News::whereNull('category_id')->count();
echo "\nNews without category: $newsWithoutCategory\n";

if ($newsWithoutCategory > 0) {
    echo "Assigning default category to news without category...\n";
    
    $defaultCategory = Category::first();
    if ($defaultCategory) {
        News::whereNull('category_id')->update(['category_id' => $defaultCategory->id]);
        echo "✅ Assigned {$newsWithoutCategory} news to default category: {$defaultCategory->name}\n";
    }
}

echo "\n=== CATEGORY CHECK COMPLETED ===\n";

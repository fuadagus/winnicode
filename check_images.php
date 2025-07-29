<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$news = App\Models\News::where('status', 'Published')->get();

echo "Berita dengan gambar:\n";
foreach($news as $item) {
    echo "- " . $item->title . " | Gambar: " . ($item->image ?? 'tidak ada') . "\n";
}

echo "\nTotal berita published: " . $news->count() . "\n";
echo "Berita dengan gambar: " . $news->whereNotNull('image')->count() . "\n";

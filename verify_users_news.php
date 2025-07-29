<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "✅ USERS & NEWS SEEDER VERIFICATION\n";
echo "=====================================\n\n";

// Verify Users by Role
echo "👥 USERS BY ROLE:\n";
echo "-----------------\n";

$superAdmins = App\Models\User::role('Super Admin')->get();
echo "Super Admins (" . $superAdmins->count() . "):\n";
foreach($superAdmins as $user) {
    echo "  - {$user->name} ({$user->email})\n";
}

$editors = App\Models\User::role('Editor')->get();
echo "\nEditors (" . $editors->count() . "):\n";
foreach($editors as $user) {
    echo "  - {$user->name} ({$user->email})\n";
}

$writers = App\Models\User::role('Writer')->get();
echo "\nWriters (" . $writers->count() . "):\n";
foreach($writers as $user) {
    echo "  - {$user->name} ({$user->email})\n";
}

// Verify News with Authors
echo "\n\n📰 NEWS WITH AUTHORS:\n";
echo "---------------------\n";

$news = App\Models\News::with(['author', 'category'])->get();
foreach($news as $article) {
    $authorRole = $article->author->roles->first()->name ?? 'No Role';
    echo "• {$article->title}\n";
    echo "  Author: {$article->author->name} ({$authorRole})\n";
    echo "  Category: {$article->category->name}\n";
    echo "  Status: {$article->status}\n";
    echo "  Views: " . number_format($article->views) . "\n\n";
}

// Statistics
echo "📊 STATISTICS:\n";
echo "---------------\n";
echo "Total Users: " . App\Models\User::count() . "\n";
echo "Total News: " . App\Models\News::count() . "\n";
echo "Published News: " . App\Models\News::where('status', 'Published')->count() . "\n";
echo "Pending News: " . App\Models\News::where('status', 'Pending')->count() . "\n";
echo "Draft News: " . App\Models\News::where('status', 'Draft')->count() . "\n";

echo "\n🔑 LOGIN CREDENTIALS:\n";
echo "======================\n";
echo "Super Admin: superadmin@gmail.com / superadmin123\n";
echo "Editor: editor@gmail.com / editor123\n";
echo "Writer: writer@gmail.com / writer123\n";
echo "\nAll users ready for testing! 🎉\n";

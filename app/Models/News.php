<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class News extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = 'news';
    // Remove automatic eager loading to avoid conflicts
    // protected $with = ['author'];
    
    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'meta_title',
        'meta_description',
        'keywords',
        'image',
        'status',
        'user_id',
        'category_id',
        'location',
        'city',
        'province',
        'country',
        'latitude',
        'longitude',
        'views',
        'published_at',
        'tags'
    ];
    
    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'published_at' => 'datetime',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }
    
    /**
     * Calculate distance from user location
     */
    public function distanceFrom($userLat, $userLng)
    {
        if (!$this->latitude || !$this->longitude) {
            return null;
        }
        
        // Haversine formula to calculate distance
        $earthRadius = 6371; // Earth radius in kilometers
        
        $latDiff = deg2rad($this->latitude - $userLat);
        $lngDiff = deg2rad($this->longitude - $userLng);
        
        $a = sin($latDiff / 2) * sin($latDiff / 2) +
             cos(deg2rad($userLat)) * cos(deg2rad($this->latitude)) *
             sin($lngDiff / 2) * sin($lngDiff / 2);
             
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        return $earthRadius * $c; // Distance in kilometers
    }
    
    /**
     * Get location display text
     */
    public function getLocationAttribute()
    {
        $location = [];
        if ($this->city) $location[] = $this->city;
        if ($this->province) $location[] = $this->province;
        if ($this->country && $this->country !== 'Indonesia') $location[] = $this->country;
        
        return implode(', ', $location) ?: 'Lokasi tidak tersedia';
    }
}

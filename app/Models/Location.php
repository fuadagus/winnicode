<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'latitude',
        'longitude',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_location');
    }

    public function news()
    {
        return $this->hasMany(News::class);
    }
}
